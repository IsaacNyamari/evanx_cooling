<?php

namespace App\Console\Commands;

use App\Support\SitemapBuilder;
use Illuminate\Console\Command;

class SitemapGenerate extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Regenerate the sitemap served at /sitemap.xml';

    public function handle(SitemapBuilder $sitemap): int
    {
        $result = $sitemap->generate();

        $this->info("Sitemap generated: {$result['total']} URLs ({$result['pages']} pages, {$result['products']} products).");
        $this->line($sitemap->publicUrl());

        return self::SUCCESS;
    }
}
