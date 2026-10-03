<?php

namespace App\Support;

use App\Models\Product;
use App\Models\SeoPage;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Single source of truth for a page's title, description, social (Open Graph) image and robots tags.
 * Order of precedence: values saved in Admin > SEO, then the built-in defaults below
 * (static pages) or automatically generated text (products).
 */
class Seo
{
    public const SITE_KEY = '_site';

    public const DEFAULT_OG_IMAGE = 'img/og-default.jpg';

    private ?Collection $rows = null;

    /**
     * Static pages: route name => label, group and default title/description.
     * Titles are kept to 60 characters and descriptions to 160 so Google shows them in full.
     *
     * @return array<string,array{label:string,group:string,title:string,description:string}>
     */
    public static function pages(): array
    {
        return [
            'home' => ['label' => 'Home', 'group' => 'Main pages',
                'title' => 'Evanx Cooling Systems | HVAC & Refrigeration Nairobi',
                'description' => 'HVAC and refrigeration experts in Nairobi, Kenya: AC installation, repair and maintenance, plus cold room parts and tools. Get a free quote today.'],
            'shop' => ['label' => 'Shop', 'group' => 'Main pages',
                'title' => 'Buy HVAC & Refrigeration Parts in Kenya | Evanx Cooling',
                'description' => 'Shop air conditioners, compressors, refrigeration parts and HVAC tools in Kenya. Browse the full catalogue and order quickly on WhatsApp.'],
            'services' => ['label' => 'Services', 'group' => 'Main pages',
                'title' => 'HVAC Services in Nairobi | Evanx Cooling Systems',
                'description' => 'AC installation, cooling and heating services, maintenance and repair, indoor air quality and annual inspections in Nairobi. Free quotes.'],
            'about' => ['label' => 'About us', 'group' => 'Main pages',
                'title' => 'About Evanx Cooling Systems | Nairobi HVAC Experts',
                'description' => 'Meet Evanx Cooling Systems, a Nairobi HVAC and refrigeration company founded in 2023 by Evans Macharia. Certified technicians and honest pricing.'],
            'contact' => ['label' => 'Contact', 'group' => 'Main pages',
                'title' => 'Contact Evanx Cooling Systems | Free HVAC Quote',
                'description' => 'Contact Evanx Cooling Systems in Nairobi for AC installation, repair and refrigeration parts. Call {phone} or send a message for a free quote.'],
            'ac.installation' => ['label' => 'AC installation', 'group' => 'Service pages',
                'title' => 'AC Installation in Nairobi | Evanx Cooling Systems',
                'description' => 'Professional air conditioner installation for homes and businesses in Nairobi. Correct sizing, neat piping and a tested install. Get a free quote.'],
            'ac.cooling' => ['label' => 'Cooling services', 'group' => 'Service pages',
                'title' => 'Cooling Services & AC Repair Nairobi | Evanx Cooling',
                'description' => 'Fast AC repair, servicing and emergency cooling services across Nairobi. Experienced technicians fix leaks, faulty compressors and poor cooling.'],
            'heating.services' => ['label' => 'Heating services', 'group' => 'Service pages',
                'title' => 'Heating Services in Nairobi | Evanx Cooling Systems',
                'description' => 'Heating system installation, repair and maintenance in Nairobi. Keep your home or business warm and efficient with certified technicians.'],
            'indoor.air.quality' => ['label' => 'Indoor air quality', 'group' => 'Service pages',
                'title' => 'Indoor Air Quality Solutions Nairobi | Evanx Cooling',
                'description' => 'Improve the air you breathe with purification, filtration, humidity control and ventilation solutions from Evanx Cooling Systems in Nairobi.'],
            'maintenace.and.repair' => ['label' => 'Maintenance & repair', 'group' => 'Service pages',
                'title' => 'HVAC Maintenance & Repair Nairobi | Evanx Cooling',
                'description' => 'Preventative HVAC maintenance plans and quick repairs in Nairobi. Cut breakdowns and energy bills with regular servicing from Evanx Cooling Systems.'],
            'annual.inspection' => ['label' => 'Annual inspection', 'group' => 'Service pages',
                'title' => 'Annual HVAC Inspection in Nairobi | Evanx Cooling',
                'description' => 'Book an annual HVAC inspection in Nairobi. Our technicians check performance, safety and efficiency so small issues never become costly failures.'],
        ];
    }

    /** Saved overrides keyed by page key (one query per request). */
    private function rows(): Collection
    {
        return $this->rows ??= SeoPage::all()->keyBy('key');
    }

    public function forgetCache(): void
    {
        $this->rows = null;
    }

    public function override(string $key): ?SeoPage
    {
        return $this->rows()->get($key);
    }

    public function brand(): string
    {
        return config('app.name');
    }

    /** Default social share image: the one set in Admin > SEO, else the generated card. */
    public function defaultImage(): string
    {
        $site = $this->override(self::SITE_KEY);

        return $site?->image ? SeoPage::resolveImage($site->image) : asset(self::DEFAULT_OG_IMAGE);
    }

    /** True while the generated default card (1200x630) is in use. */
    public function usingGeneratedImage(): bool
    {
        return ! $this->override(self::SITE_KEY)?->image;
    }

    /** @return array<string,mixed> */
    public function forPage(string $key): array
    {
        $default = self::pages()[$key] ?? ['title' => $this->brand(), 'description' => self::pages()['home']['description']];
        $row = $this->override($key);

        $title = $row?->meta_title ?: $default['title'];
        $description = $row?->meta_description ?: str_replace('{phone}', (string) config('site.phone'), $default['description']);
        $image = $row?->image ? SeoPage::resolveImage($row->image) : $this->defaultImage();

        return $this->payload([
            'title' => $title,
            'description' => $description,
            'image' => $image,
            'url' => isset(self::pages()[$key]) ? route($key) : url()->current(),
            'robots' => $row?->noindex ? 'noindex,follow' : null,
            'custom' => (bool) ($row?->meta_title || $row?->meta_description),
        ]);
    }

    /** @return array<string,mixed> */
    public function forProduct(Product $product): array
    {
        return $this->payload([
            'title' => $product->meta_title ?: $this->autoProductTitle($product),
            'description' => $product->meta_description ?: $this->autoProductDescription($product),
            'image' => $product->hasImage() ? $product->imageUrl() : $this->defaultImage(),
            'url' => route('shop.show', $product->slug),
            'type' => 'product',
            'custom' => (bool) ($product->meta_title || $product->meta_description),
            'json_ld' => $this->productJsonLd($product),
        ]);
    }

    /** What the current request should output in <head>. */
    public function current(): array
    {
        $route = request()->route()?->getName();

        if ($route === 'shop.show' && ($product = request()->attributes->get('seo.product'))) {
            return $this->forProduct($product);
        }

        if (isset(self::pages()[$route])) {
            $seo = $this->forPage($route);

            // Search-result and filtered shop URLs are duplicates of /shop: keep them out of Google.
            if ($route === 'shop' && request()->hasAny(['q', 'sort'])) {
                $seo['robots'] = 'noindex,follow';
            }

            return $seo;
        }

        return array_merge($this->forPage('home'), ['title' => $this->brand(), 'robots' => 'noindex,follow']);
    }

    public function autoProductTitle(Product $product): string
    {
        $brand = $this->brand();

        foreach (["{$product->name} in Kenya | {$brand}", "{$product->name} | {$brand}", "{$product->name} | Evanx Cooling"] as $candidate) {
            if (mb_strlen($candidate) <= SeoAnalyzer::TITLE_MAX) {
                return $candidate;
            }
        }

        return Str::limit($product->name, SeoAnalyzer::TITLE_MAX - 3, '...', true);
    }

    public function autoProductDescription(Product $product): string
    {
        $brand = $this->brand();
        $summary = $product->summary(400);

        if ($summary === '') {
            // Long product names: fall back to shorter wording so it still fits in 160 characters.
            foreach ([
                "Buy {$product->name} in Nairobi, Kenya from {$brand}. Quality HVAC and refrigeration parts at fair prices. Order quickly on WhatsApp.",
                "Buy {$product->name} in Nairobi, Kenya from {$brand}. Quality HVAC and refrigeration parts. Order on WhatsApp.",
                "Buy {$product->name} from {$brand}, Nairobi. Order on WhatsApp.",
            ] as $candidate) {
                if (mb_strlen($candidate) <= SeoAnalyzer::DESC_MAX) {
                    return $candidate;
                }
            }

            return Str::limit("Buy {$product->name} from {$brand}, Nairobi.", SeoAnalyzer::DESC_MAX - 1, '…', true);
        }

        // Prefer whole sentences; add a call to action if it still fits.
        $max = SeoAnalyzer::DESC_MAX;
        $text = '';
        foreach (preg_split('/(?<=[.!?])\s+/u', $summary) as $sentence) {
            if (mb_strlen(trim($text.' '.$sentence)) > $max) {
                break;
            }
            $text = trim($text.' '.$sentence);
        }

        if ($text === '') {
            return Str::limit($summary, $max - 1, '…', true); // one very long sentence: cut at a word
        }

        $tail = " Order today from {$brand}.";

        return mb_strlen($text.$tail) <= $max ? $text.$tail : $text;
    }

    /** @return array<string,mixed> */
    private function productJsonLd(Product $product): array
    {
        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'description' => $product->summary(300) ?: $product->name,
            'image' => $product->galleryUrls(),
            'brand' => ['@type' => 'Brand', 'name' => $this->brand()],
            'url' => route('shop.show', $product->slug),
        ];

        if ($product->sku) {
            $data['sku'] = $product->sku;
        }

        // Only advertise an offer when there is a real price (Google rejects 0-priced offers).
        if ($product->hasPrice()) {
            $data['offers'] = [
                '@type' => 'Offer',
                'priceCurrency' => $product->currency,
                'price' => number_format($product->currentPrice(), 2, '.', ''),
                'availability' => $product->in_stock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'url' => route('shop.show', $product->slug),
                'seller' => ['@type' => 'Organization', 'name' => $this->brand()],
            ];
        }

        return $data;
    }

    /** @param array<string,mixed> $data */
    private function payload(array $data): array
    {
        $generated = $data['image'] === asset(self::DEFAULT_OG_IMAGE);

        return array_merge([
            'type' => 'website',
            'robots' => null,
            'json_ld' => null,
            'image_width' => $generated ? 1200 : null,
            'image_height' => $generated ? 630 : null,
        ], $data, [
            'robots' => ($data['robots'] ?? null) ?: 'index,follow,max-image-preview:large',
            'site_name' => $this->brand(),
        ]);
    }
}
