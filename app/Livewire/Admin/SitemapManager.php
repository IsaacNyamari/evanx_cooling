<?php

namespace App\Livewire\Admin;

use App\Support\SitemapBuilder;
use Livewire\Component;

class SitemapManager extends Component
{
    public ?string $message = null;

    public ?string $error = null;

    public function generate(SitemapBuilder $sitemap): void
    {
        $this->message = $this->error = null;

        try {
            $result = $sitemap->generate();
            $this->message = "Sitemap generated with {$result['total']} URLs ({$result['pages']} pages and {$result['products']} products).";
        } catch (\Throwable $e) {
            $this->error = $e->getMessage();
        }
    }

    public function render(SitemapBuilder $sitemap)
    {
        return view('livewire.admin.sitemap-manager', [
            'url' => $sitemap->publicUrl(),
            'exists' => $sitemap->exists(),
            'generatedAt' => $sitemap->generatedAt(),
            'count' => $sitemap->urlCount(),
            'stale' => $sitemap->isStale(),
        ]);
    }
}
