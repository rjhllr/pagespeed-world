<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class MarketingPageController extends Controller
{
    protected int $cacheMinutes = 60;

    protected function renderCached(string $locale, string $key, string $view, array $data = [])
    {
        app()->setLocale($locale);

        $cacheKey = implode(':', ['marketing', 'page', $locale, $key, sha1(request()->getRequestUri())]);

        $html = Cache::remember($cacheKey, now()->addMinutes($this->cacheMinutes), function () use ($view, $data) {
            return view($view, $data)->render();
        });

        return response($html);
    }

    public function home(string $locale)
    {
        $meta = [
            'description' => __('marketing.meta.description'),
            'og_title' => __('marketing.meta.og_title'),
            'og_description' => __('marketing.meta.og_description'),
            'url' => url($locale),
        ];

        return $this->renderCached($locale, 'home', 'marketing.home', [
            'title' => __('marketing.meta.title'),
            'meta' => $meta,
        ]);
    }

    public function features(string $locale)
    {
        return $this->renderCached($locale, 'features', 'marketing.features', [
            'title' => __('marketing.features.title'),
            'meta' => [
                'description' => __('marketing.features.description'),
                'url' => url($locale . '/features'),
            ],
        ]);
    }

    public function pricing(string $locale)
    {
        return $this->renderCached($locale, 'pricing', 'marketing.pricing', [
            'title' => __('marketing.pricing.title'),
            'meta' => [
                'description' => __('marketing.pricing.description'),
                'url' => url($locale . '/pricing'),
            ],
        ]);
    }

    public function faq(string $locale)
    {
        return $this->renderCached($locale, 'faq', 'marketing.faq', [
            'title' => __('marketing.faq.title'),
            'meta' => [
                'description' => __('marketing.faq.description'),
                'url' => url($locale . '/faq'),
            ],
        ]);
    }

    public function about(string $locale)
    {
        return $this->renderCached($locale, 'about', 'marketing.about', [
            'title' => __('marketing.about.title'),
            'meta' => [
                'description' => __('marketing.about.description'),
                'url' => url($locale . '/about'),
            ],
        ]);
    }

    public function contact(string $locale)
    {
        return $this->renderCached($locale, 'contact', 'marketing.contact', [
            'title' => __('marketing.contact.title'),
            'meta' => [
                'description' => __('marketing.contact.description'),
                'url' => url($locale . '/contact'),
            ],
        ]);
    }
}
