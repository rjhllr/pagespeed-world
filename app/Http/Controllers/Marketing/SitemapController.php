<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $cacheKey = 'marketing:sitemap';

        $xml = Cache::remember($cacheKey, now()->addMinutes(60), function () {
            $locales = ['en', 'de'];
            $pages = ['', '/features', '/pricing', '/faq', '/about', '/contact', '/blog'];
            $posts = config('blog.posts', []);

            $urls = [];

            foreach ($locales as $locale) {
                foreach ($pages as $page) {
                    $loc = url($locale . $page);
                    $urls[] = [
                        'loc' => $loc,
                        'lastmod' => now()->toDateString(),
                    ];
                }
                foreach ($posts as $post) {
                    $urls[] = [
                        'loc' => url($locale . '/blog/' . $post['slug']),
                        'lastmod' => $post['date'] ?? now()->toDateString(),
                    ];
                }
            }

            return view('marketing.sitemap', ['urls' => $urls])->render();
        });

        return Response::make($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
