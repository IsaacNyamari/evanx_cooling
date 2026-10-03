<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FaviconTest extends TestCase
{
    use RefreshDatabase;

    public function test_favicon_files_are_real_images_of_the_right_size(): void
    {
        foreach ([
            'favicon-16x16.png' => 16, 'favicon-32x32.png' => 32, 'apple-touch-icon.png' => 180,
            'android-chrome-192x192.png' => 192, 'android-chrome-512x512.png' => 512, 'img/icon/faviconV2.png' => 512,
        ] as $file => $size) {
            $info = getimagesize(public_path($file));
            $this->assertSame([$size, $size, IMAGETYPE_PNG], [$info[0], $info[1], $info[2]], $file);
        }

        // favicon.ico must not be empty (it used to be 0 bytes) and holds 16, 32 and 48px images.
        $ico = file_get_contents(public_path('favicon.ico'));
        $this->assertGreaterThan(1000, strlen($ico));
        $this->assertSame([0, 1, 3], array_values(array_slice(unpack('v3', $ico), 0, 3)));

        $manifest = json_decode(file_get_contents(public_path('site.webmanifest')), true);
        $this->assertSame('Evanx Cooling Systems', $manifest['name']);
        foreach ($manifest['icons'] as $icon) {
            $this->assertFileExists(public_path(ltrim($icon['src'], '/')));
        }
    }

    public function test_every_layout_links_the_favicon(): void
    {
        $paths = ['/', '/shop', '/contact', '/login', '/this-page-does-not-exist'];

        foreach ($paths as $path) {
            $this->get($path)
                ->assertSee('favicon.ico', false)
                ->assertSee('favicon-32x32.png', false)
                ->assertSee('apple-touch-icon.png', false);
        }

        // The admin dashboard and its pages (a different layout from the public site).
        $this->actingAs(User::factory()->create());
        foreach (['/admin', '/admin/products', '/admin/categories', '/admin/messages'] as $path) {
            $this->get($path)
                ->assertOk()
                ->assertSee('favicon.ico', false)
                ->assertSee('favicon-32x32.png', false)
                ->assertSee('apple-touch-icon.png', false);
        }
    }
}
