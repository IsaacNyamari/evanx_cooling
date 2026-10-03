<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ShopDownloadImages extends Command
{
    protected $signature = 'shop:download-images';

    protected $description = 'Download remote product/category images into public/uploads so they never break';

    private const EXTENSIONS = [
        'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif', 'image/avif' => 'avif',
    ];

    /** @var array<string,?string> remote URL => local path (null when it failed) */
    private array $done = [];

    public function handle(): int
    {
        // 1. Collect every distinct remote URL.
        $jobs = []; // url => slug used for the file name
        foreach (Product::all() as $product) {
            foreach (array_filter(array_merge([$product->image], $product->gallery ?? [])) as $url) {
                if ($this->isRemote($url)) {
                    $jobs[$url] = $product->slug;
                }
            }
        }
        foreach (Category::all() as $category) {
            if ($this->isRemote($category->image)) {
                $jobs[$category->image] = 'category-'.$category->slug;
            }
        }

        $this->info(count($jobs).' remote images to download.');
        $bar = $this->output->createProgressBar(count($jobs));

        // 2. Download in small concurrent batches.
        foreach (array_chunk($jobs, 8, true) as $batch) {
            $urls = array_keys($batch);
            $responses = Http::pool(fn (Pool $pool) => array_map(
                fn ($url) => $pool->as($url)->timeout(40)->retry(2, 500, throw: false)->withHeaders(['User-Agent' => 'Mozilla/5.0'])->get($url),
                $urls
            ));

            foreach ($urls as $url) {
                $this->done[$url] = $this->store($url, $batch[$url], $responses[$url] ?? null);
                $bar->advance();
            }
        }
        $bar->finish();
        $this->newLine(2);

        // 3. Point the database at the local copies.
        $this->rewrite();

        $failed = array_keys(array_filter($this->done, fn ($path) => $path === null));
        $this->info((count($this->done) - count($failed)).' downloaded, '.count($failed).' failed.');
        foreach ($failed as $url) {
            $this->warn("  failed: $url");
        }
        if ($failed) {
            $this->line('Failed images keep their original URL; re-run this command to retry them. Missing files fall back to a placeholder.');
        }

        // 4. Final check: every local reference must exist on disk.
        $missing = $this->verify();
        $missing === 0
            ? $this->info('Verified: every local image reference exists on disk.')
            : $this->error("$missing local image references point to missing files.");

        return $missing === 0 && ! $failed ? self::SUCCESS : self::FAILURE;
    }

    private function isRemote(?string $path): bool
    {
        return $path && Str::startsWith($path, ['http://', 'https://']);
    }

    private function store(string $url, string $slug, $response): ?string
    {
        if (! $response || $response instanceof \Throwable || ! method_exists($response, 'successful') || ! $response->successful()) {
            return null;
        }

        $body = $response->body();
        $info = @getimagesizefromstring($body);
        if ($info === false || ! isset(self::EXTENSIONS[$info['mime']])) {
            return null; // not a real image (e.g. an HTML error page)
        }

        $dir = Str::startsWith($slug, 'category-') ? 'categories' : 'products';
        $path = $dir.'/'.Str::limit(Str::slug(urldecode($slug)), 80, '').'-'.substr(md5($url), 0, 8).'.'.self::EXTENSIONS[$info['mime']];

        return Storage::disk('uploads')->put($path, $body) ? $path : null;
    }

    private function rewrite(): void
    {
        foreach (Product::all() as $product) {
            $image = $this->map($product->image);
            $gallery = array_values(array_filter(array_map(fn ($g) => $this->map($g), $product->gallery ?? [])));
            if ($image !== $product->image || $gallery !== ($product->gallery ?? [])) {
                $product->update(['image' => $image, 'gallery' => $gallery ?: null]);
            }
        }
        foreach (Category::all() as $category) {
            if (($image = $this->map($category->image)) !== $category->image) {
                $category->update(['image' => $image]);
            }
        }
    }

    private function map(?string $path): ?string
    {
        return $this->done[$path] ?? $path;
    }

    private function verify(): int
    {
        $missing = 0;
        $check = function (?string $path) use (&$missing) {
            if ($path && ! $this->isRemote($path) && ! is_file(public_path('uploads/'.$path))) {
                $missing++;
            }
        };
        foreach (Product::all() as $p) {
            $check($p->image);
            array_map($check, $p->gallery ?? []);
        }
        foreach (Category::all() as $c) {
            $check($c->image);
        }

        return $missing;
    }
}
