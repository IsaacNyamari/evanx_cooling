<?php

namespace Tests\Feature;

use App\Livewire\Admin\SitemapManager;
use App\Models\Product;
use App\Models\User;
use App\Support\SitemapBuilder;
use Database\Seeders\ShopSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        @unlink((new SitemapBuilder)->path());
    }

    protected function tearDown(): void
    {
        @unlink((new SitemapBuilder)->path());
        parent::tearDown();
    }

    public function test_sitemap_lists_pages_and_visible_products_with_absolute_urls(): void
    {
        $this->seed(ShopSeeder::class);
        Product::firstWhere('slug', 'flare-nuts')->update(['is_active' => false]);

        $response = $this->get('/sitemap.xml')->assertOk();
        $this->assertStringContainsString('application/xml', $response->headers->get('Content-Type'));
        $xml = $response->getContent();

        foreach (['/', '/shop', '/services', '/about-us', '/contact', '/services/ac-installation-by-evanx-cooling-systems'] as $path) {
            $this->assertStringContainsString('<loc>http://localhost'.rtrim($path === '/' ? '' : $path, '/').'</loc>', $xml, $path);
        }

        $this->assertStringContainsString('<loc>http://localhost/shop/', $xml);
        $this->assertStringNotContainsString('/shop/flare-nuts<', $xml, 'hidden products are excluded');
        $this->assertStringNotContainsString('127.0.0.1', $xml);
        $this->assertStringNotContainsString('/admin', $xml);
        $this->assertStringContainsString('<image:loc>', $xml);
        $this->assertStringContainsString('<lastmod>', $xml);
        $this->assertSame(194 + 11, substr_count($xml, '<url>'));
    }

    public function test_generated_file_is_served_and_urls_use_app_url_from_the_command_line(): void
    {
        $this->seed(ShopSeeder::class);
        config(['app.url' => 'https://example-shop.test/']); // trailing slash, as in a typical .env

        $this->artisan('sitemap:generate')->assertSuccessful();

        $builder = new SitemapBuilder;
        $this->assertTrue($builder->exists());
        $xml = file_get_contents($builder->path());
        $this->assertStringContainsString('<loc>https://example-shop.test/shop</loc>', $xml);
        $this->assertStringNotContainsString('test//', $xml);
        $this->assertStringNotContainsString('http://localhost', $xml);

        $response = $this->get('/sitemap.xml')->assertOk();
        $this->assertSame(realpath($builder->path()), $response->baseResponse->getFile()->getRealPath());
    }

    public function test_robots_txt_blocks_admin_and_points_to_the_sitemap(): void
    {
        $this->get('/robots.txt')->assertOk()
            ->assertSee('Disallow: /admin', false)
            ->assertSee('Sitemap: http://localhost/sitemap.xml', false);
    }

    public function test_admin_page_generates_shows_link_and_offers_download(): void
    {
        $this->seed(ShopSeeder::class);
        $this->actingAs(User::factory()->create());

        $this->get('/admin/sitemap')->assertOk()
            ->assertSee('http://localhost/sitemap.xml')
            ->assertSee('Not generated yet')
            ->assertSee('Search Console');

        Livewire::test(SitemapManager::class)
            ->call('generate')
            ->assertSee('Sitemap generated with 206 URLs')
            ->assertSee('Up to date');

        $this->assertTrue((new SitemapBuilder)->exists());

        // Editing a product makes the file out of date until regenerated.
        $this->travel(1)->minutes();
        Product::first()->update(['price' => 10]);
        Livewire::test(SitemapManager::class)->assertSee('Out of date');

        $this->get('/admin/sitemap/download')->assertOk()->assertDownload('sitemap.xml');
    }

    public function test_download_and_page_require_login(): void
    {
        $this->get('/admin/sitemap')->assertRedirect('/login');
        $this->get('/admin/sitemap/download')->assertRedirect('/login');
    }

    public function test_the_old_static_files_and_route_are_gone(): void
    {
        $this->assertFileDoesNotExist(public_path('sitemap.xml'));
        $this->assertFileDoesNotExist(public_path('robots.txt'));
        $this->get('/generate-sitemap')->assertNotFound();
    }
}
