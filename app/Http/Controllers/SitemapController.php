<?php

namespace App\Http\Controllers;

use App\Support\SitemapBuilder;

class SitemapController extends Controller
{
    /** /sitemap.xml: the generated file, or a freshly built one if none has been generated yet. */
    public function index(SitemapBuilder $sitemap)
    {
        if ($sitemap->exists()) {
            return response()->file($sitemap->path(), ['Content-Type' => 'application/xml; charset=UTF-8']);
        }

        return response($sitemap->xml(), 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /** /robots.txt: keeps crawlers out of the admin area and points them at the sitemap. */
    public function robots(SitemapBuilder $sitemap)
    {
        $body = "User-agent: *\nDisallow: /admin\nDisallow: /login\nDisallow: /forgot-password\nDisallow: /reset-password\n\nSitemap: ".$sitemap->publicUrl()."\n";

        return response($body, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    /** Admin > Sitemap > Download. */
    public function download(SitemapBuilder $sitemap)
    {
        if (! $sitemap->exists()) {
            $sitemap->generate();
        }

        return response()->download($sitemap->path(), 'sitemap.xml', ['Content-Type' => 'application/xml']);
    }
}
