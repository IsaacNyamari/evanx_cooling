<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Seeds categories and products from the JSON snapshot in database/seeders/data
 * (taken from the supplier's WooCommerce Store API), re-branded for Evanx Cooling Systems.
 *
 * Safe to re-run at any time: it adds catalogue items that are missing and repairs broken
 * slugs / image links on existing ones. Everything else on existing records is left alone,
 * so edits made in the admin (prices, descriptions, categories...) survive every run.
 */
class ShopSeeder extends Seeder
{
    public function run(): void
    {
        $categories = json_decode(file_get_contents(database_path('seeders/data/categories.json')), true);
        $products = json_decode(file_get_contents(database_path('seeders/data/products.json')), true);

        DB::transaction(function () use ($categories, $products) {
            $map = [];
            $created = [];

            foreach ($categories as $row) {
                $slug = Str::slug(urldecode($row['slug']));
                $category = Category::where('external_id', $row['id'])->first();

                if ($category) {
                    if (! preg_match('/^[a-z0-9-]+$/', $category->slug)) {
                        $category->update(['slug' => $slug]);
                    }
                } else {
                    $category = Category::create([
                        'external_id' => $row['id'],
                        'name' => $this->brand($row['name']),
                        'slug' => $slug,
                        'description' => $row['description'] ? $this->brand($row['description']) : null,
                        'image' => $row['image'] ?: null,
                    ]);
                    $created[$row['id']] = true;
                }

                $map[$row['id']] = $category;
            }

            // Second pass so parents exist regardless of order (new categories only).
            foreach ($categories as $row) {
                if (isset($created[$row['id']]) && $row['parent'] && isset($map[$row['parent']])) {
                    $map[$row['id']]->update(['parent_id' => $map[$row['parent']]->id]);
                }
            }

            foreach ($products as $row) {
                $slug = Str::slug(urldecode($row['slug']));
                $images = array_map(fn ($url) => $this->localImage($url), array_column($row['images'], 'src'));
                $existing = Product::where('external_id', $row['id'])->first();

                if ($existing) {
                    $repair = [];

                    if (! preg_match('/^[a-z0-9-]+$/', $existing->slug)) {
                        $repair['slug'] = $slug;
                    }

                    // Re-link images only when the stored one is missing, remote or stale.
                    $imageOk = $existing->image
                        && ! Str::startsWith($existing->image, 'http')
                        && is_file(public_path('uploads/'.$existing->image));
                    if (! $imageOk && $images) {
                        $repair['image'] = $images[0];
                        $repair['gallery'] = array_slice($images, 1) ?: null;
                    }

                    if ($repair) {
                        $existing->update($repair);
                    }

                    continue;
                }

                $minor = 10 ** ($row['minor_unit'] ?? 2);

                $product = Product::create([
                    'external_id' => $row['id'],
                    'name' => $this->brand($row['name']),
                    'slug' => $slug,
                    'sku' => $row['sku'] ?: null,
                    'short_description' => $this->clean($row['short_description']),
                    'description' => $this->clean($row['description']),
                    'price' => $row['regular_price'] / $minor,
                    'sale_price' => $row['on_sale'] ? $row['sale_price'] / $minor : null,
                    'currency' => 'KES',
                    'image' => $images[0] ?? null,
                    'gallery' => array_slice($images, 1) ?: null,
                    'in_stock' => $row['in_stock'],
                ]);

                $product->categories()->sync(
                    collect($row['categories'])->map(fn ($id) => $map[$id]->id ?? null)->filter()->all()
                );
            }
        });
    }

    /**
     * Use the copy already in public/uploads (see shop:download-images) when it exists,
     * otherwise keep the remote URL so the command can fetch it later.
     */
    private function localImage(string $url): string
    {
        $pattern = public_path('uploads/products/*-'.substr(md5($url), 0, 8).'.*');

        return ($match = glob($pattern)[0] ?? null) ? 'products/'.basename($match) : $url;
    }

    /** Swap the supplier's brand name for ours. */
    private function brand(string $text): string
    {
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5);

        return str_ireplace(
            ['Coolmass Refrigeration', 'Coolmas Refrigeration', 'Coolmass', 'Coolmas'],
            ['Evanx Cooling Systems', 'Evanx Cooling Systems', 'Evanx', 'Evanx'],
            $text
        );
    }

    /** Rebrand descriptions, replace contact details and strip everything but simple markup. */
    private function clean(?string $html): ?string
    {
        if (blank($html)) {
            return null;
        }

        $phone = config('site.phone');
        $email = 'info@evanxcoolingsystems.co.ke';

        // Drop links pointing at the old site/social page (keep their text), then rebrand.
        $html = preg_replace('#<a\b[^>]*href="https?://(?:www\.)?(?:coolmassrefrigeration\.com|facebook\.com)[^"]*"[^>]*>(.*?)</a>#is', '$1', $html);
        $html = preg_replace('#<a\b[^>]*href="mailto:[^"]*"[^>]*>(.*?)</a>#is', '<a href="mailto:'.$email.'">'.$email.'</a>', $html);
        $html = preg_replace('#coolmassrefrigeration@yahoo\.com#i', $email, $html);
        $html = preg_replace('#\+254\s?745\s?322\s?538#', $phone, $html);
        $html = $this->brand($html);

        $html = strip_tags($html, '<p><br><h2><h3><h4><ul><ol><li><strong><em><b><i><a><table><thead><tbody><tr><th><td>');
        $html = preg_replace('/\s(?:data-[\w-]+|class|style|rel)="[^"]*"/i', '', $html);
        $html = preg_replace('#<(p|li)>\s*</\1>#i', '', $html);

        return trim($html);
    }
}
