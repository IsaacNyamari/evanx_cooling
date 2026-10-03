<?php

namespace Tests\Feature;

use App\Livewire\Admin\ProductForm;
use App\Livewire\Admin\Seo\Overview;
use App\Livewire\Admin\Seo\Pages;
use App\Livewire\Admin\Seo\Products;
use App\Models\Product;
use App\Models\SeoPage;
use App\Models\User;
use App\Support\Seo;
use App\Support\SeoAnalyzer;
use App\Support\SeoAudit;
use Database\Seeders\ShopSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    private function meta(string $html, string $selector): ?string
    {
        // property="og:title" / name="description" / rel="canonical" -> content / href
        $pattern = '/<(?:meta|link)\s+'.preg_quote($selector, '/').'\s+(?:content|href)="([^"]*)"/';

        return preg_match($pattern, $html, $m) ? html_entity_decode($m[1]) : null;
    }

    private function title(string $html): ?string
    {
        return preg_match('#<title>(.*?)</title>#s', $html, $m) ? html_entity_decode(trim($m[1])) : null;
    }

    public function test_default_page_titles_and_descriptions_fit_google_snippets(): void
    {
        foreach (Seo::pages() as $key => $page) {
            $title = SeoAnalyzer::title($page['title']);
            $description = SeoAnalyzer::description(str_replace('{phone}', '+254707856908', $page['description']));

            $this->assertSame('good', $title['status'], "$key title: {$page['title']} ({$title['length']})");
            $this->assertSame('good', $description['status'], "$key description ({$description['length']})");
        }
    }

    public function test_every_public_page_has_unique_complete_seo_tags(): void
    {
        $titles = [];
        $descriptions = [];

        foreach (array_keys(Seo::pages()) as $key) {
            $html = $this->get(route($key))->assertOk()->getContent();

            $title = $this->title($html);
            $description = $this->meta($html, 'name="description"');
            $this->assertNotEmpty($title, $key);
            $this->assertNotEmpty($description, $key);
            $titles[] = $title;
            $descriptions[] = $description;

            $this->assertSame($title, $this->meta($html, 'property="og:title"'), "$key og:title");
            $this->assertSame($description, $this->meta($html, 'property="og:description"'));
            $this->assertSame(route($key), $this->meta($html, 'rel="canonical"'), "$key canonical");
            $this->assertSame(route($key), $this->meta($html, 'property="og:url"'));
            $this->assertStringStartsWith('http', $this->meta($html, 'property="og:image"'), "$key og:image absolute");
            $this->assertSame('summary_large_image', $this->meta($html, 'name="twitter:card"'));
            $this->assertSame('index,follow,max-image-preview:large', $this->meta($html, 'name="robots"'));
            $this->assertSame(1, substr_count($html, '<title>'), "$key has exactly one <title>");
        }

        $this->assertSame($titles, array_values(array_unique($titles)), 'page titles must be unique');
        $this->assertSame($descriptions, array_values(array_unique($descriptions)), 'page descriptions must be unique');
    }

    public function test_contact_description_uses_the_real_phone_number(): void
    {
        config(['site.phone' => '+254707856908']);
        $html = $this->get(route('contact'))->getContent();

        $this->assertStringContainsString('+254707856908', $this->meta($html, 'name="description"'));
        $this->assertStringNotContainsString('700 123', $html);
    }

    public function test_default_share_image_exists_and_is_1200_by_630(): void
    {
        $info = getimagesize(public_path('img/og-default.jpg'));
        $this->assertSame([1200, 630], [$info[0], $info[1]]);

        $html = $this->get(route('home'))->getContent();
        $this->assertSame(asset('img/og-default.jpg'), $this->meta($html, 'property="og:image"'));
        $this->assertSame('1200', $this->meta($html, 'property="og:image:width"'));
    }

    public function test_every_product_gets_a_valid_automatic_title_and_description(): void
    {
        $this->seed(ShopSeeder::class);
        $seo = app(Seo::class);

        Product::all()->each(function (Product $p) use ($seo) {
            $this->assertLessThanOrEqual(60, mb_strlen($seo->autoProductTitle($p)), $p->name);
            $this->assertStringContainsString(mb_substr($p->name, 0, 20), $seo->autoProductTitle($p));
            $description = $seo->autoProductDescription($p);
            $this->assertLessThanOrEqual(160, mb_strlen($description), $p->name);
            $this->assertGreaterThan(40, mb_strlen($description), $p->name);
            $this->assertStringNotContainsString('Phone:', $description);
            $this->assertStringNotContainsString('<', $description);
        });
    }

    public function test_product_page_has_product_tags_image_and_structured_data(): void
    {
        $this->seed(ShopSeeder::class);
        $product = Product::firstWhere('slug', 'flare-nuts');

        $html = $this->get(route('shop.show', 'flare-nuts'))->assertOk()->getContent();

        $this->assertSame('Flare Nuts in Kenya | Evanx Cooling Systems', $this->title($html));
        $this->assertSame('product', $this->meta($html, 'property="og:type"'));
        $this->assertSame($product->imageUrl(), $this->meta($html, 'property="og:image"'));
        $this->assertSame($product->imageUrl(), $this->meta($html, 'name="twitter:image"'));
        $this->assertSame(route('shop.show', 'flare-nuts'), $this->meta($html, 'rel="canonical"'));

        preg_match('#<script type="application/ld\+json">(.*?)</script>#s', $html, $m);
        $json = json_decode($m[1], true);
        $this->assertSame('Product', $json['@type']);
        $this->assertSame('Flare Nuts', $json['name']);
        $this->assertContains($product->imageUrl(), $json['image']);
        $this->assertArrayNotHasKey('offers', $json, 'no offer while the price is 0');

        $product->update(['price' => 450, 'sku' => 'FN-1']);
        $json = $this->productJson('flare-nuts');
        $this->assertSame('450.00', $json['offers']['price']);
        $this->assertSame('KES', $json['offers']['priceCurrency']);
        $this->assertSame('https://schema.org/InStock', $json['offers']['availability']);
        $this->assertSame('FN-1', $json['sku']);
    }

    private function productJson(string $slug): array
    {
        preg_match('#<script type="application/ld\+json">(.*?)</script>#s', $this->get(route('shop.show', $slug))->getContent(), $m);

        return json_decode($m[1], true);
    }

    public function test_custom_product_meta_overrides_the_automatic_text(): void
    {
        $this->seed(ShopSeeder::class);
        Product::firstWhere('slug', 'flare-nuts')->update(['meta_title' => 'Buy Flare Nuts Online Kenya', 'meta_description' => 'Brass flare nuts, all sizes, fast delivery.']);

        $html = $this->get(route('shop.show', 'flare-nuts'))->getContent();

        $this->assertSame('Buy Flare Nuts Online Kenya', $this->title($html));
        $this->assertSame('Brass flare nuts, all sizes, fast delivery.', $this->meta($html, 'name="description"'));
        $this->assertSame('Buy Flare Nuts Online Kenya', $this->meta($html, 'property="og:title"'));
    }

    public function test_search_results_admin_and_login_pages_are_kept_out_of_google(): void
    {
        $this->seed(ShopSeeder::class);

        $this->assertSame('noindex,follow', $this->meta($this->get('/shop?q=pipe')->getContent(), 'name="robots"'));
        $this->assertSame('index,follow,max-image-preview:large', $this->meta($this->get('/shop')->getContent(), 'name="robots"'));
        $this->assertSame('noindex,nofollow', $this->meta($this->get('/login')->getContent(), 'name="robots"'));

        $this->actingAs(User::factory()->create());
        $this->assertSame('noindex,nofollow', $this->meta($this->get('/admin')->getContent(), 'name="robots"'));
    }

    public function test_saved_page_overrides_win_over_defaults(): void
    {
        SeoPage::create(['key' => 'about', 'meta_title' => 'Custom About Title', 'meta_description' => 'Custom about description for search.', 'noindex' => true]);

        $html = $this->get(route('about'))->getContent();

        $this->assertSame('Custom About Title', $this->title($html));
        $this->assertSame('Custom about description for search.', $this->meta($html, 'name="description"'));
        $this->assertSame('noindex,follow', $this->meta($html, 'name="robots"'));
        // Other pages are unaffected.
        $this->assertSame(Seo::pages()['services']['title'], $this->title($this->get(route('services'))->getContent()));
    }

    public function test_admin_can_edit_a_page_upload_a_share_image_and_reset(): void
    {
        $this->actingAs(User::factory()->create());

        $component = Livewire::test(Pages::class)
            ->call('edit', 'services')
            ->set('meta_title', 'Services Title Custom')
            ->set('meta_description', 'A custom services description.')
            ->set('image_file', UploadedFile::fake()->image('share.jpg', 1200, 630))
            ->call('save')
            ->assertHasNoErrors();

        $row = SeoPage::firstWhere('key', 'services');
        $this->assertSame('Services Title Custom', $row->meta_title);
        $this->assertStringStartsWith('seo/', $row->image);
        $this->assertFileExists(public_path('uploads/'.$row->image));

        $html = $this->get(route('services'))->getContent();
        $this->assertSame('Services Title Custom', $this->title($html));
        $this->assertSame(asset('uploads/'.$row->image), $this->meta($html, 'property="og:image"'));

        $file = public_path('uploads/'.$row->image);
        $component->call('resetToDefault');
        $this->assertNull(SeoPage::firstWhere('key', 'services'));
        $this->assertFileDoesNotExist($file);
        $this->assertSame(Seo::pages()['services']['title'], $this->title($this->get(route('services'))->getContent()));
    }

    public function test_site_wide_default_share_image_can_be_replaced(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(Pages::class)
            ->set('site_image_file', UploadedFile::fake()->image('brand.png', 1200, 630))
            ->call('saveSiteImage')->assertHasNoErrors();

        $site = SeoPage::firstWhere('key', Seo::SITE_KEY);
        $this->assertFileExists(public_path('uploads/'.$site->image));
        $this->assertSame(asset('uploads/'.$site->image), $this->meta($this->get(route('home'))->getContent(), 'property="og:image"'));

        $file = public_path('uploads/'.$site->image);
        Livewire::test(Pages::class)->call('removeSiteImage');
        $this->assertFileDoesNotExist($file);
        $this->assertSame(asset('img/og-default.jpg'), $this->meta($this->get(route('home'))->getContent(), 'property="og:image"'));
    }

    public function test_non_image_share_upload_is_rejected(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(Pages::class)->call('edit', 'home')
            ->set('image_file', UploadedFile::fake()->create('x.pdf', 10, 'application/pdf'))
            ->assertHasErrors('image_file')->assertSet('image_file', null);
    }

    public function test_admin_can_customise_a_product_from_the_seo_screen_and_the_product_form(): void
    {
        $this->seed(ShopSeeder::class);
        $this->actingAs(User::factory()->create());
        $product = Product::firstWhere('slug', 'flare-nuts');

        Livewire::test(Products::class)
            ->call('edit', $product->id)
            ->call('useSuggestion')
            ->assertSet('meta_title', 'Flare Nuts in Kenya | Evanx Cooling Systems')
            ->set('meta_title', 'Flare Nuts for AC and Cold Rooms | Evanx')
            ->call('save')->assertHasNoErrors();
        $this->assertSame('Flare Nuts for AC and Cold Rooms | Evanx', $product->fresh()->meta_title);

        Livewire::test(Products::class)->call('edit', $product->id)->call('resetToAuto');
        $this->assertNull($product->fresh()->meta_title);

        Livewire::test(ProductForm::class, ['product' => $product])
            ->set('meta_description', 'Form-entered description for flare nuts.')
            ->call('save')->assertHasNoErrors();
        $this->assertSame('Form-entered description for flare nuts.', $product->fresh()->meta_description);

        Livewire::test(Products::class)->set('filter', 'custom')->assertSee('Flare Nuts');
        Livewire::test(Products::class)->set('meta_title', str_repeat('x', 200))->set('editingId', $product->id)->call('save')->assertHasErrors('meta_title');
    }

    public function test_seo_admin_screens_require_login_and_render(): void
    {
        foreach (['/admin/seo', '/admin/seo/pages', '/admin/seo/products'] as $path) {
            $this->get($path)->assertRedirect('/login');
        }

        $this->seed(ShopSeeder::class);
        $this->actingAs(User::factory()->create());

        $this->get('/admin/seo')->assertOk()->assertSee('Checklist')->assertSee('Products to improve');
        $this->get('/admin/seo/pages')->assertOk()->assertSee('Default share image')->assertSee('Service pages');
        $this->get('/admin/seo/products')->assertOk()->assertSee(Product::orderBy('name')->value('name'));
        $this->get('/admin')->assertSee('SEO');
    }

    public function test_seo_audit_scores_the_site_and_reacts_to_problems(): void
    {
        $this->seed(ShopSeeder::class);

        $audit = app(SeoAudit::class)->run();
        $this->assertGreaterThanOrEqual(0, $audit['score']);
        $this->assertLessThanOrEqual(100, $audit['score']);
        $this->assertSame(195, $audit['products']['total']);
        $this->assertSame(0, $audit['products']['no_image']);
        $this->assertSame(0, $audit['pages']['issues'], 'built-in page defaults should all be good');

        $statuses = collect($audit['checks'])->pluck('status', 'label');
        $this->assertSame('bad', $statuses['Sitemap'], 'no sitemap generated yet');

        // Break things: a hidden page, a product without a photo and a too-long custom title.
        SeoPage::create(['key' => 'about', 'noindex' => true]);
        $product = Product::firstWhere('slug', 'flare-nuts');
        $product->update(['image' => null, 'gallery' => null, 'meta_title' => str_repeat('Very long title ', 8)]);

        $after = app(SeoAudit::class)->run();
        $this->assertLessThan($audit['score'], $after['score']);
        $this->assertSame(1, $after['products']['no_image']);
        $this->assertSame('warn', collect($after['checks'])->pluck('status', 'label')['Pages visible to Google']);

        Livewire::actingAs(User::factory()->create())->test(Overview::class)->assertSee((string) $after['score']);
    }

    public function test_analyzer_rates_lengths(): void
    {
        $this->assertSame('bad', SeoAnalyzer::title('')['status']);
        $this->assertSame('warn', SeoAnalyzer::title('Too short')['status']);
        $this->assertSame('good', SeoAnalyzer::title(str_repeat('a', 55))['status']);
        $this->assertSame('warn', SeoAnalyzer::title(str_repeat('a', 65))['status']);
        $this->assertSame('bad', SeoAnalyzer::title(str_repeat('a', 80))['status']);
        $this->assertSame('good', SeoAnalyzer::description(str_repeat('a', 150))['status']);
        $this->assertSame('warn', SeoAnalyzer::description(str_repeat('a', 30))['status']);
    }
}
