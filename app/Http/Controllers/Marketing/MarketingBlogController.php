<?php

namespace App\Http\Controllers\Marketing;

use App\Helpers\BlogContentProcessor;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MarketingBlogController extends Controller
{
    protected int $cacheMinutes = 60;

    protected function setLocale(string $locale): void
    {
        app()->setLocale($locale);
    }

    protected function posts(): array
    {
        return config('blog.posts', []);
    }

    public function index(string $locale)
    {
        $this->setLocale($locale);

        $cacheKey = implode(':', ['marketing', 'blog-index', $locale, sha1(request()->getRequestUri())]);

        $html = Cache::remember($cacheKey, now()->addMinutes($this->cacheMinutes), function () use ($locale) {
            $posts = collect($this->posts())
                ->map(function ($post) use ($locale) {
                    $localized = $post['locales'][$locale] ?? $post['locales']['en'] ?? null;
                    if (!$localized) {
                        return null;
                    }

                    return [
                        'slug' => $post['slug'],
                        'title' => $localized['title'] ?? '',
                        'excerpt' => $localized['excerpt'] ?? '',
                        'date' => $post['date'] ?? null,
                        'reading_time' => $post['reading_time'] ?? null,
                        'category' => $post['category'] ?? null,
                    ];
                })
                ->filter()
                ->sortByDesc('date')
                ->values();

            $meta = [
                'description' => __('marketing.blog.description'),
                'url' => url($locale . '/blog'),
            ];

            return view('marketing.blog.index', [
                'title' => __('marketing.blog.title'),
                'posts' => $posts,
                'meta' => $meta,
            ])->render();
        });

        return response($html);
    }

    public function show(string $locale, string $slug)
    {
        $this->setLocale($locale);

        $post = collect($this->posts())->firstWhere('slug', $slug);

        if (!$post) {
            throw new NotFoundHttpException();
        }

        $localized = $post['locales'][$locale] ?? null;

        if (!$localized) {
            throw new NotFoundHttpException();
        }

        $cacheKey = implode(':', ['marketing', 'blog-show', $locale, $slug, sha1(request()->getRequestUri())]);

        $html = Cache::remember($cacheKey, now()->addMinutes($this->cacheMinutes), function () use ($locale, $slug, $post, $localized) {
            // Process blog content to generate TOC and enhance formatting
            $processed = BlogContentProcessor::process($localized['body'] ?? '');

            $meta = [
                'description' => Str::limit(strip_tags($localized['body'] ?? ''), 160),
                'url' => url($locale . '/blog/' . $slug),
            ];

            return view('marketing.blog.show', [
                'title' => $localized['title'] ?? $post['slug'],
                'post' => $post,
                'content' => array_merge($localized, ['body' => $processed['html']]),
                'toc' => $processed['toc'],
                'meta' => $meta,
            ])->render();
        });

        return response($html);
    }
}
