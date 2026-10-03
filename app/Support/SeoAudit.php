<?php

namespace App\Support;

use App\Models\Product;

/** Scores the site's SEO setup for the Admin > SEO overview. */
class SeoAudit
{
    public function __construct(private Seo $seo, private SitemapBuilder $sitemap) {}

    /** @return array<string,mixed> */
    public function run(): array
    {
        $this->seo->forgetCache();

        $pages = $this->pages();
        $products = $this->products();
        $checks = $this->checks($pages, $products);

        $pageScore = $pages['count'] ? $pages['points'] / $pages['count'] : 1;
        $productScore = $products['total'] ? $products['points'] / $products['total'] : 1;
        $techScore = collect($checks)->where('status', '!=', 'info')->avg(fn ($c) => ['good' => 1, 'warn' => 0.5, 'bad' => 0][$c['status']]) ?? 1;

        return [
            'score' => (int) round(($pageScore * 0.25 + $productScore * 0.35 + $techScore * 0.40) * 100),
            'pages' => $pages,
            'products' => $products,
            'checks' => $checks,
        ];
    }

    /** @return array<string,mixed> */
    private function pages(): array
    {
        $rows = [];
        $points = 0;

        foreach (Seo::pages() as $key => $meta) {
            $data = $this->seo->forPage($key);
            $title = SeoAnalyzer::title($data['title']);
            $description = SeoAnalyzer::description($data['description']);
            $status = SeoAnalyzer::worst($title, $description);
            $points += ['good' => 1, 'warn' => 0.5, 'bad' => 0][$status];

            $rows[] = [
                'key' => $key, 'label' => $meta['label'], 'title' => $data['title'], 'description' => $data['description'],
                'status' => $status, 'custom' => $data['custom'], 'noindex' => str_starts_with($data['robots'], 'noindex'),
            ];
        }

        return ['rows' => $rows, 'count' => count($rows), 'points' => $points, 'issues' => collect($rows)->where('status', '!=', 'good')->count()];
    }

    /** @return array<string,mixed> */
    private function products(): array
    {
        $items = Product::active()->get(['id', 'name', 'slug', 'short_description', 'meta_title', 'meta_description', 'image']);

        $rows = $items->map(function (Product $p) {
            $title = $p->meta_title ?: $this->seo->autoProductTitle($p);
            $description = $p->meta_description ?: $this->seo->autoProductDescription($p);

            return [
                'id' => $p->id, 'name' => $p->name, 'title' => $title, 'description' => $description,
                'custom' => (bool) ($p->meta_title || $p->meta_description),
                'has_image' => $p->hasImage(),
                'title_rating' => SeoAnalyzer::title($title),
                'description_rating' => SeoAnalyzer::description($description),
            ];
        });

        $dupTitles = $rows->groupBy(fn ($r) => mb_strtolower($r['title']))->filter(fn ($g) => $g->count() > 1);
        $dupDescriptions = $rows->groupBy(fn ($r) => mb_strtolower($r['description']))->filter(fn ($g) => $g->count() > 1);
        $dupTitleIds = $dupTitles->flatten(1)->pluck('id')->all();

        $points = 0;
        $problems = [];
        foreach ($rows as $r) {
            $status = SeoAnalyzer::worst($r['title_rating'], $r['description_rating']);
            if (! $r['has_image'] || in_array($r['id'], $dupTitleIds, true)) {
                $status = $status === 'bad' ? 'bad' : 'warn';
            }
            $points += ['good' => 1, 'warn' => 0.5, 'bad' => 0][$status];
            if ($status !== 'good') {
                $problems[] = $r + ['status' => $status];
            }
        }

        return [
            'total' => $rows->count(),
            'custom' => $rows->where('custom', true)->count(),
            'no_image' => $rows->where('has_image', false)->count(),
            'short_description' => $rows->filter(fn ($r) => $r['description_rating']['status'] !== 'good')->count(),
            'duplicate_titles' => count($dupTitleIds),
            'duplicate_descriptions' => $dupDescriptions->flatten(1)->count(),
            'points' => $points,
            'problems' => array_slice($problems, 0, 8),
            'problem_count' => count($problems),
        ];
    }

    /** @return list<array{label:string,status:string,detail:string,link?:string,action?:string}> */
    private function checks(array $pages, array $products): array
    {
        $checks = [];

        $https = str_starts_with((string) config('app.url'), 'https://');
        $checks[] = ['label' => 'Secure site (HTTPS)', 'status' => $https ? 'good' : 'warn',
            'detail' => $https ? 'APP_URL uses https, so canonical links and the sitemap use https.' : 'Set APP_URL to your https:// address so canonical links and the sitemap use it.'];

        $sitemapStatus = ! $this->sitemap->exists() ? 'bad' : ($this->sitemap->isStale() ? 'warn' : 'good');
        $checks[] = ['label' => 'Sitemap', 'status' => $sitemapStatus,
            'detail' => match ($sitemapStatus) {
                'bad' => 'No sitemap generated yet. Google uses it to find every product.',
                'warn' => 'Products changed since the sitemap was generated. Regenerate it.',
                default => $this->sitemap->urlCount().' URLs listed and up to date.',
            }, 'link' => route('admin.sitemap'), 'action' => $sitemapStatus === 'good' ? 'View' : 'Open sitemap'];

        $checks[] = ['label' => 'robots.txt', 'status' => 'good', 'detail' => 'Keeps crawlers out of /admin and login, and points them at the sitemap.', 'link' => url('/robots.txt'), 'action' => 'View'];

        $imageOk = is_file(public_path(Seo::DEFAULT_OG_IMAGE)) || ! $this->seo->usingGeneratedImage();
        $checks[] = ['label' => 'Social share image', 'status' => $imageOk ? 'good' : 'bad',
            'detail' => $imageOk ? 'Every page has a share image for Google Discover, Facebook, WhatsApp and X.' : 'The default share image file is missing.'];

        $noImage = $products['no_image'];
        $checks[] = ['label' => 'Product photos', 'status' => $noImage === 0 ? 'good' : ($noImage <= max(3, $products['total'] * 0.05) ? 'warn' : 'bad'),
            'detail' => $noImage === 0 ? 'Every product has a photo for image search and share cards.' : "{$noImage} product(s) have no photo.",
            'link' => route('admin.seo.products', ['filter' => 'noimage']), 'action' => $noImage ? 'Review' : null];

        $dup = $products['duplicate_titles'];
        $checks[] = ['label' => 'Unique product titles', 'status' => $dup === 0 ? 'good' : 'warn',
            'detail' => $dup === 0 ? 'No two products share a title.' : "{$dup} products share a title with another one. Make them distinct.",
            'link' => route('admin.seo.products', ['filter' => 'issues']), 'action' => $dup ? 'Review' : null];

        $hidden = collect($pages['rows'])->where('noindex', true)->count();
        $checks[] = ['label' => 'Pages visible to Google', 'status' => $hidden === 0 ? 'good' : 'warn',
            'detail' => $hidden === 0 ? 'No main page is set to noindex.' : "{$hidden} page(s) are hidden from Google (noindex).",
            'link' => route('admin.seo.pages'), 'action' => $hidden ? 'Review' : null];

        $checks[] = ['label' => 'Structured data', 'status' => 'good',
            'detail' => 'Product details (name, image, price when set, availability) and business details are published for rich results.'];

        $checks[] = ['label' => 'Google Search Console', 'status' => 'info',
            'detail' => 'Submit sitemap.xml once so Google finds your pages quickly (steps are on the Sitemap page).', 'link' => 'https://search.google.com/search-console', 'action' => 'Open'];

        return $checks;
    }
}
