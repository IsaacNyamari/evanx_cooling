<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Support\Facades\URL as UrlFacade;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

/**
 * Builds the public sitemap (static pages, the shop and every visible product, with images) and
 * stores it in storage/app/sitemap.xml. It is served at /sitemap.xml and can be downloaded from
 * Admin > Sitemap. Nothing is written to public/, so there is never a stale static file.
 */
class SitemapBuilder
{
    /** Page route name => [priority, change frequency] */
    private const PAGES = [
        'home' => [1.0, Url::CHANGE_FREQUENCY_WEEKLY],
        'shop' => [0.9, Url::CHANGE_FREQUENCY_DAILY],
        'services' => [0.9, Url::CHANGE_FREQUENCY_MONTHLY],
        'about' => [0.7, Url::CHANGE_FREQUENCY_MONTHLY],
        'contact' => [0.8, Url::CHANGE_FREQUENCY_MONTHLY],
        'ac.installation' => [0.8, Url::CHANGE_FREQUENCY_MONTHLY],
        'ac.cooling' => [0.8, Url::CHANGE_FREQUENCY_MONTHLY],
        'heating.services' => [0.8, Url::CHANGE_FREQUENCY_MONTHLY],
        'indoor.air.quality' => [0.8, Url::CHANGE_FREQUENCY_MONTHLY],
        'maintenace.and.repair' => [0.8, Url::CHANGE_FREQUENCY_MONTHLY],
        'annual.inspection' => [0.8, Url::CHANGE_FREQUENCY_MONTHLY],
    ];

    public function path(): string
    {
        return storage_path('app/sitemap.xml');
    }

    public function exists(): bool
    {
        return is_file($this->path());
    }

    public function generatedAt(): ?\Illuminate\Support\Carbon
    {
        return $this->exists() ? \Illuminate\Support\Carbon::createFromTimestamp(filemtime($this->path())) : null;
    }

    /** Public address search engines should be given. */
    public function publicUrl(): string
    {
        return $this->base().'/sitemap.xml';
    }

    /** True when products changed after the file was written. */
    public function isStale(): bool
    {
        $generated = $this->generatedAt();
        if (! $generated) {
            return true;
        }

        $latest = Product::active()->max('updated_at');

        return $latest && $generated->lt(\Illuminate\Support\Carbon::parse($latest));
    }

    /** Number of <loc> entries in the stored file. */
    public function urlCount(): int
    {
        return $this->exists() ? preg_match_all('#<loc>#', file_get_contents($this->path())) : 0;
    }

    public function xml(): string
    {
        return $this->build()->render();
    }

    /** @return array{total:int, pages:int, products:int} */
    public function generate(): array
    {
        $sitemap = $this->build();

        if (! is_dir(dirname($this->path()))) {
            mkdir(dirname($this->path()), 0755, true);
        }

        $sitemap->writeToFile($this->path());

        if (! $this->exists() || filesize($this->path()) === 0) {
            throw new \RuntimeException('The sitemap file could not be written to '.$this->path().'. Check that storage/app is writable.');
        }

        return [
            'total' => count($sitemap->getTags()),
            'pages' => count(self::PAGES),
            'products' => count($sitemap->getTags()) - count(self::PAGES),
        ];
    }

    public function build(): Sitemap
    {
        // Always produce absolute URLs on the real domain, also when run from the command line.
        $base = $this->base();
        UrlFacade::forceRootUrl($base);
        UrlFacade::forceScheme(parse_url($base, PHP_URL_SCHEME) ?: 'https');

        $sitemap = Sitemap::create();

        foreach (self::PAGES as $route => [$priority, $frequency]) {
            $sitemap->add(
                Url::create(url(route($route, [], false)))
                    ->setPriority($priority)
                    ->setChangeFrequency($frequency)
            );
        }

        Product::active()->orderBy('id')->each(function (Product $product) use ($sitemap) {
            $url = Url::create(route('shop.show', $product->slug))
                ->setLastModificationDate($product->updated_at)
                ->setPriority(0.6)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY);

            if ($product->hasImage()) {
                $url->addImage($product->imageUrl(), $product->name);
            }

            $sitemap->add($url);
        });

        return $sitemap;
    }

    private function base(): string
    {
        $base = app()->runningInConsole() || ! request()->getHost()
            ? config('app.url')
            : request()->getSchemeAndHttpHost();

        return rtrim($base, '/');
    }
}
