<?php

namespace App\Http\Controllers;

use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use Illuminate\Http\Request;
use App\Models\Message; // If you want to include dynamic content

class SitemapController extends Controller
{
    public function generate()
    {
        // Create a new sitemap
        $sitemap = Sitemap::create();

        // Add static pages
        $staticPages = [
            '/' => [
                'priority' => 1.0,
                'frequency' => Url::CHANGE_FREQUENCY_ALWAYS,
            ],
            '/contact' => [
                'priority' => 0.8,
                'frequency' => Url::CHANGE_FREQUENCY_ALWAYS,
            ],
            '/services' => [
                'priority' => 0.9,
                'frequency' => Url::CHANGE_FREQUENCY_ALWAYS,
            ],
            '/about-us' => [
                'priority' => 0.7,
                'frequency' => Url::CHANGE_FREQUENCY_ALWAYS,
            ],
            '/services/ac-installation-by-evanx-cooling-systems' => [
                'priority' => 0.8,
                'frequency' => Url::CHANGE_FREQUENCY_ALWAYS,
            ],
            '/services/cooling-services-by-evanx-cooling-systems' => [
                'priority' => 0.8,
                'frequency' => Url::CHANGE_FREQUENCY_ALWAYS,
            ],
            '/services/heating-services-by-evanx-cooling-systems' => [
                'priority' => 0.8,
                'frequency' => Url::CHANGE_FREQUENCY_ALWAYS,
            ],
            '/services/indoor-air-quality-by-evanx-cooling-systems' => [
                'priority' => 0.8,
                'frequency' => Url::CHANGE_FREQUENCY_ALWAYS,
            ],
            '/services/maintenance-and-repair-by-evanx-cooling-systems' => [
                'priority' => 0.8,
                'frequency' => Url::CHANGE_FREQUENCY_ALWAYS,
            ],
            '/services/hvac-annual-inspection-at-evanx-cooling-systems' => [
                'priority' => 0.8,
                'frequency' => Url::CHANGE_FREQUENCY_ALWAYS,
            ],
        ];

        foreach ($staticPages as $path => $options) {
            $sitemap->add(
                Url::create($path)
                    ->setPriority($options['priority'])
                    ->setChangeFrequency($options['frequency'])
            );
        }

        // Add dynamic pages if needed (example with blog posts or services from database)
        // Uncomment and modify based on your actual models
        /*
        $messages = Message::where('is_published', true)->get();
        foreach ($messages as $message) {
            $sitemap->add(
                Url::create("/admin/messages/{$message->id}")
                    ->setPriority(0.5)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_ALWAYS)
                    ->setLastModified($message->updated_at)
            );
        }
        */

        // Write to file
        $sitemap->writeToFile(public_path('sitemap.xml'));

        return response()->json(['message' => 'Sitemap generated successfully!']);
    }

    // Method to serve the sitemap dynamically
    public function index()
    {
        if (file_exists(public_path('sitemap.xml'))) {
            return response()->file(public_path('sitemap.xml'), [
                'Content-Type' => 'application/xml'
            ]);
        }

        // Generate on the fly if file doesn't exist
        $sitemap = $this->generateSitemap();
        return response($sitemap->render(), 200)
            ->header('Content-Type', 'application/xml');
    }

    private function generateSitemap()
    {
        $sitemap = Sitemap::create();

        // Add all static routes
        $sitemap->add(Url::create('/')->setPriority(1.0));
        $sitemap->add(Url::create('/contact')->setPriority(0.8));
        $sitemap->add(Url::create('/services')->setPriority(0.9));
        $sitemap->add(Url::create('/about-us')->setPriority(0.7));
        $sitemap->add(Url::create('/services/ac-installation-by-evanx-cooling-systems')->setPriority(0.8));
        $sitemap->add(Url::create('/services/cooling-services-by-evanx-cooling-systems')->setPriority(0.8));
        $sitemap->add(Url::create('/services/heating-services-by-evanx-cooling-systems')->setPriority(0.8));
        $sitemap->add(Url::create('/services/indoor-air-quality-by-evanx-cooling-systems')->setPriority(0.8));
        $sitemap->add(Url::create('/services/maintenance-and-repair-by-evanx-cooling-systems')->setPriority(0.8));
        $sitemap->add(Url::create('/services/hvac-annual-inspection-at-evanx-cooling-systems')->setPriority(0.8));

        return $sitemap;
    }
}