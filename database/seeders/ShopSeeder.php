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
 * Safe to re-run: records are matched on their external_id.
 */
class ShopSeeder extends Seeder
{
    public function run(): void
    {
        $categories = json_decode(file_get_contents(database_path('seeders/data/categories.json')), true);
        $products = json_decode(file_get_contents(database_path('seeders/data/products.json')), true);

        DB::transaction(function () use ($categories, $products) {
            $map = [];

            foreach ($categories as $row) {
                $map[$row['id']] = Category::updateOrCreate(
                    ['external_id' => $row['id']],
                    [
                        'name' => $this->brand($row['name']),
                        'slug' => $row['slug'],
                        'description' => $row['description'] ? $this->brand($row['description']) : null,
                        'image' => $row['image'] ?: null,
                    ]
                );
            }

            // Second pass so parents exist regardless of order.
            foreach ($categories as $row) {
                if ($row['parent'] && isset($map[$row['parent']])) {
                    $map[$row['id']]->update(['parent_id' => $map[$row['parent']]->id]);
                }
            }

            foreach ($products as $row) {
                $minor = 10 ** ($row['minor_unit'] ?? 2);
                $existing = Product::where('external_id', $row['id'])->first();
                $keepImages = $existing && $existing->image && ! Str::startsWith($existing->image, 'http');
                $images = array_map(fn ($url) => $this->localImage($url), array_column($row['images'], 'src'));

                $product = Product::updateOrCreate(
                    ['external_id' => $row['id']],
                    [
                        'name' => $this->brand($row['name']),
                        'slug' => $row['slug'],
                        'sku' => $row['sku'] ?: null,
                        'short_description' => $this->clean($row['short_description']),
                        'description' => $this->clean($row['description']),
                        'price' => $row['regular_price'] / $minor,
                        'sale_price' => $row['on_sale'] ? $row['sale_price'] / $minor : null,
                        'currency' => 'KES',
                        // Never overwrite images already stored locally (e.g. changed in the admin).
                        'image' => $keepImages ? $existing->image : ($images[0] ?? null),
                        'gallery' => $keepImages ? $existing->gallery : (array_slice($images, 1) ?: null),
                        'in_stock' => $row['in_stock'],
                    ]
                );

                $product->categories()->sync(
                    collect($row['categories'])->map(fn($id) => $map[$id]->id ?? null)->filter()->all()
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
        $html = preg_replace('#<a\b[^>]*href="mailto:[^"]*"[^>]*>(.*?)</a>#is', '<a href="mailto:' . $email . '">' . $email . '</a>', $html);
        $html = preg_replace('#coolmassrefrigeration@yahoo\.com#i', $email, $html);
        $html = preg_replace('#\+254\s?745\s?322\s?538#', $phone, $html);
        $html = $this->brand($html);

        $html = strip_tags($html, '<p><br><h2><h3><h4><ul><ol><li><strong><em><b><i><a><table><thead><tbody><tr><th><td>');
        $html = preg_replace('/\s(?:data-[\w-]+|class|style|rel)="[^"]*"/i', '', $html);
        $html = preg_replace('#<(p|li)>\s*</\1>#i', '', $html);

        return trim($html);
    }
}
