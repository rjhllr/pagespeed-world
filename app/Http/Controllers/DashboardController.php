<?php

namespace App\Http\Controllers;

use App\Models\BundleSize;
use App\Models\CrawlResult;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $pagesQuery = Page::with(['latestMobileResult', 'latestDesktopResult', 'latestBundleSize'])
            ->orderBy('name');

        if (!$user->is_admin) {
            $organizationIds = $user->organizations()->pluck('organizations.id');
            $pagesQuery->whereIn('organization_id', $organizationIds);
        }

        $pages = $pagesQuery->get();

        return view('dashboard.index', compact('pages'));
    }

    public function show(Page $page)
    {
        $user = Auth::user();
        
        // Ensure user can only view their organization's pages
        if (
            !$user->is_admin &&
            !$user->organizations()->whereKey($page->organization_id)->exists()
        ) {
            abort(403);
        }

        $page->load(['organization', 'latestMobileResult', 'latestDesktopResult', 'latestBundleSize']);

        // Get historical data for charts (last 30 days)
        $mobileResults = CrawlResult::where('page_id', $page->id)
            ->where('strategy', 'mobile')
            ->where('status', 'success')
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('created_at')
            ->get();

        $desktopResults = CrawlResult::where('page_id', $page->id)
            ->where('strategy', 'desktop')
            ->where('status', 'success')
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('created_at')
            ->get();

        // Get bundle size history
        $bundleSizes = BundleSize::where('page_id', $page->id)
            ->where('status', 'success')
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('created_at')
            ->get();

        $latestFilmstripBundle = $page->bundleSizes()
            ->where('status', 'success')
            ->whereNotNull('filmstrip')
            ->where('filmstrip', '!=', '[]')
            ->latest()
            ->first();

        $filmstripFrames = $latestFilmstripBundle ? $latestFilmstripBundle->getFilmstripUrls() : [];
        $filmstripMeta = $latestFilmstripBundle ? [
            'capturedAt' => $latestFilmstripBundle->created_at?->toDateTimeString(),
            'loadTime' => $latestFilmstripBundle->load_time,
            'domContentLoaded' => $latestFilmstripBundle->dom_content_loaded,
            'requestCount' => $latestFilmstripBundle->total_requests,
            'totalSizeFormatted' => $latestFilmstripBundle->total_size ? BundleSize::formatBytes($latestFilmstripBundle->total_size) : null,
            'transferSizeFormatted' => $latestFilmstripBundle->total_transfer_size ? BundleSize::formatBytes($latestFilmstripBundle->total_transfer_size) : null,
        ] : null;

        // Prepare chart data
        $chartData = $this->prepareChartData($mobileResults, $desktopResults, $bundleSizes);

        $heroBackgroundUrl = null;
        if (!empty($filmstripFrames)) {
            $heroBackgroundUrl = $filmstripFrames[array_key_last($filmstripFrames)]['url'] ?? null;
        }

        return view('dashboard.show', compact(
            'page',
            'mobileResults',
            'desktopResults',
            'bundleSizes',
            'chartData',
            'filmstripFrames',
            'filmstripMeta',
            'heroBackgroundUrl'
        ));
    }

    public function pageMetrics(Page $page)
    {
        $user = Auth::user();
        
        if (
            !$user->is_admin &&
            !$user->organizations()->whereKey($page->organization_id)->exists()
        ) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $days = request('days', 30);
        
        $mobileResults = CrawlResult::where('page_id', $page->id)
            ->where('strategy', 'mobile')
            ->where('status', 'success')
            ->where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at')
            ->get();

        $desktopResults = CrawlResult::where('page_id', $page->id)
            ->where('strategy', 'desktop')
            ->where('status', 'success')
            ->where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at')
            ->get();

        return response()->json($this->prepareChartData($mobileResults, $desktopResults));
    }

    public function account()
    {
        $user = Auth::user();
        $organizations = $user->organizations()->withCount(['users', 'pages'])->get();

        return view('dashboard.account', compact('user', 'organizations'));
    }

    private function prepareChartData($mobileResults, $desktopResults, $bundleSizes = null)
    {
        [$bucketLabels, $bucketedMobile, $bucketedDesktop] = $this->buildBucketedSeries($mobileResults, $desktopResults);

        $data = [
            'performance' => [
                'mobile' => [
                    'dates' => $mobileResults->pluck('created_at')->map(fn ($d) => $d->format('M d H:i'))->toArray(),
                    'scores' => $mobileResults->pluck('performance_score')->map(fn ($s) => $s !== null ? round($s, 1) : null)->toArray(),
                ],
                'desktop' => [
                    'dates' => $desktopResults->pluck('created_at')->map(fn ($d) => $d->format('M d H:i'))->toArray(),
                    'scores' => $desktopResults->pluck('performance_score')->map(fn ($s) => $s !== null ? round($s, 1) : null)->toArray(),
                ],
            ],
            'webVitals' => [
                'mobile' => [
                    'fcp' => $mobileResults->pluck('first_contentful_paint')->map(fn ($v) => $v !== null ? round($v) : null)->toArray(),
                    'lcp' => $mobileResults->pluck('largest_contentful_paint')->map(fn ($v) => $v !== null ? round($v) : null)->toArray(),
                    'tbt' => $mobileResults->pluck('total_blocking_time')->map(fn ($v) => $v !== null ? round($v) : null)->toArray(),
                    'cls' => $mobileResults->pluck('cumulative_layout_shift')->map(fn ($v) => $v !== null ? round($v, 3) : null)->toArray(),
                    'dates' => $mobileResults->pluck('created_at')->map(fn ($d) => $d->format('M d H:i'))->toArray(),
                ],
                'desktop' => [
                    'fcp' => $desktopResults->pluck('first_contentful_paint')->map(fn ($v) => $v !== null ? round($v) : null)->toArray(),
                    'lcp' => $desktopResults->pluck('largest_contentful_paint')->map(fn ($v) => $v !== null ? round($v) : null)->toArray(),
                    'tbt' => $desktopResults->pluck('total_blocking_time')->map(fn ($v) => $v !== null ? round($v) : null)->toArray(),
                    'cls' => $desktopResults->pluck('cumulative_layout_shift')->map(fn ($v) => $v !== null ? round($v, 3) : null)->toArray(),
                    'dates' => $desktopResults->pluck('created_at')->map(fn ($d) => $d->format('M d H:i'))->toArray(),
                ],
            ],
            'bucketed' => [
                'labels' => $bucketLabels,
                'mobile' => $bucketedMobile,
                'desktop' => $bucketedDesktop,
                'smoothing' => true,
            ],
            'scores' => [
                'mobile' => [
                    'performance' => $mobileResults->last()?->performance_score ? round($mobileResults->last()->performance_score) : null,
                    'accessibility' => $mobileResults->last()?->accessibility_score ? round($mobileResults->last()->accessibility_score) : null,
                    'bestPractices' => $mobileResults->last()?->best_practices_score ? round($mobileResults->last()->best_practices_score) : null,
                    'seo' => $mobileResults->last()?->seo_score ? round($mobileResults->last()->seo_score) : null,
                ],
                'desktop' => [
                    'performance' => $desktopResults->last()?->performance_score ? round($desktopResults->last()->performance_score) : null,
                    'accessibility' => $desktopResults->last()?->accessibility_score ? round($desktopResults->last()->accessibility_score) : null,
                    'bestPractices' => $desktopResults->last()?->best_practices_score ? round($desktopResults->last()->best_practices_score) : null,
                    'seo' => $desktopResults->last()?->seo_score ? round($desktopResults->last()->seo_score) : null,
                ],
            ],
        ];

        // Add bundle size data if available
        if ($bundleSizes) {
            $latestBundle = $bundleSizes->last();
            $data['bundleSize'] = [
                'dates' => $bundleSizes->pluck('created_at')->map(fn ($d) => $d->format('M d H:i'))->toArray(),
                // Sizes in KB
                'total' => $bundleSizes->pluck('total_size')->map(fn ($v) => $v ? round($v / 1024) : null)->toArray(),
                'totalTransfer' => $bundleSizes->pluck('total_transfer_size')->map(fn ($v) => $v ? round($v / 1024) : null)->toArray(),
                'javascript' => $bundleSizes->pluck('javascript_size')->map(fn ($v) => $v ? round($v / 1024) : null)->toArray(),
                'css' => $bundleSizes->pluck('css_size')->map(fn ($v) => $v ? round($v / 1024) : null)->toArray(),
                'images' => $bundleSizes->pluck('image_size')->map(fn ($v) => $v ? round($v / 1024) : null)->toArray(),
                'fonts' => $bundleSizes->pluck('font_size')->map(fn ($v) => $v ? round($v / 1024) : null)->toArray(),
                'html' => $bundleSizes->pluck('html_size')->map(fn ($v) => $v ? round($v / 1024) : null)->toArray(),
                // Download times in ms
                'jsDownloadTime' => $bundleSizes->pluck('javascript_download_time')->toArray(),
                'cssDownloadTime' => $bundleSizes->pluck('css_download_time')->toArray(),
                'imageDownloadTime' => $bundleSizes->pluck('image_download_time')->toArray(),
                'loadTime' => $bundleSizes->pluck('load_time')->toArray(),
                // Compression and slow requests
                'compressionRatio' => $bundleSizes->pluck('compression_ratio')->toArray(),
                'slowRequests' => $bundleSizes->pluck('slow_request_count')->toArray(),
                'current' => [
                    'total' => $latestBundle?->total_size,
                    'totalFormatted' => $latestBundle ? BundleSize::formatBytes($latestBundle->total_size ?? 0) : null,
                    'totalTransfer' => $latestBundle?->total_transfer_size,
                    'totalTransferFormatted' => $latestBundle ? BundleSize::formatBytes($latestBundle->total_transfer_size ?? 0) : null,
                    'javascript' => $latestBundle?->javascript_size,
                    'javascriptFormatted' => $latestBundle ? BundleSize::formatBytes($latestBundle->javascript_size ?? 0) : null,
                    'css' => $latestBundle?->css_size,
                    'cssFormatted' => $latestBundle ? BundleSize::formatBytes($latestBundle->css_size ?? 0) : null,
                    'images' => $latestBundle?->image_size,
                    'imagesFormatted' => $latestBundle ? BundleSize::formatBytes($latestBundle->image_size ?? 0) : null,
                    'fonts' => $latestBundle?->font_size,
                    'fontsFormatted' => $latestBundle ? BundleSize::formatBytes($latestBundle->font_size ?? 0) : null,
                    'html' => $latestBundle?->html_size,
                    'htmlFormatted' => $latestBundle ? BundleSize::formatBytes($latestBundle->html_size ?? 0) : null,
                    'requests' => $latestBundle?->total_requests,
                    'loadTime' => $latestBundle?->load_time,
                    'compressionRatio' => $latestBundle?->compression_ratio,
                    'slowRequests' => $latestBundle?->slow_request_count,
                ],
            ];
        }

        return $data;
    }

    /**
     * Build 30-day bucketed (hourly) series for mobile and desktop, averaging values per bucket.
     *
     * @return array [labels, mobileBuckets, desktopBuckets]
     */
    private function buildBucketedSeries(Collection $mobileResults, Collection $desktopResults): array
    {
        $earliest = collect([
            $mobileResults->min('created_at'),
            $desktopResults->min('created_at'),
        ])->filter()->min();

        $latest = collect([
            $mobileResults->max('created_at'),
            $desktopResults->max('created_at'),
        ])->filter()->max() ?? now();

        // Use available range, capped to last 30 days to avoid empty leading space
        $start = ($earliest ? $earliest->clone() : now()->subDays(30))->startOfHour();
        $floor = now()->subDays(30)->startOfHour();
        if ($start->lt($floor)) {
            $start = $floor;
        }

        $end = $latest->clone()->startOfHour();
        if ($end->lt($start)) {
            $end = $start;
        }

        $hours = $start->diffInHours($end);

        $labels = [];
        $mobileBuckets = $this->emptyBucketStructure($hours + 1);
        $desktopBuckets = $this->emptyBucketStructure($hours + 1);

        for ($i = 0; $i <= $hours; $i++) {
            $bucketTime = $start->clone()->addHours($i);
            $labels[] = $bucketTime->format('M d H:00');
        }

        $this->accumulateBuckets($mobileBuckets, $mobileResults, $start);
        $this->accumulateBuckets($desktopBuckets, $desktopResults, $start);

        $mobileAverages = $this->finalizeBuckets($mobileBuckets);
        $desktopAverages = $this->finalizeBuckets($desktopBuckets);

        return [$labels, $mobileAverages, $desktopAverages];
    }

    /**
     * Initialize bucket storage for metrics.
     */
    private function emptyBucketStructure(int $bucketCount): array
    {
        $makeArray = fn () => array_fill(0, $bucketCount, null);

        return [
            'performance' => $makeArray(),
            'fcp' => $makeArray(),
            'lcp' => $makeArray(),
            'tbt' => $makeArray(),
            'cls' => $makeArray(),
        ];
    }

    /**
     * Accumulate sums/counts in buckets.
     */
    private function accumulateBuckets(array &$buckets, Collection $results, $start): void
    {
        $sums = [
            'performance' => [],
            'fcp' => [],
            'lcp' => [],
            'tbt' => [],
            'cls' => [],
        ];
        $counts = [
            'performance' => [],
            'fcp' => [],
            'lcp' => [],
            'tbt' => [],
            'cls' => [],
        ];

        foreach ($results as $result) {
            if ($result->created_at->lt($start)) {
                continue;
            }

            $bucketIndex = $start->diffInHours($result->created_at->startOfHour());
            if (!array_key_exists($bucketIndex, $buckets['performance'])) {
                continue;
            }

            $this->accumulateValue($sums, $counts, 'performance', $bucketIndex, $result->performance_score);
            $this->accumulateValue($sums, $counts, 'fcp', $bucketIndex, $result->first_contentful_paint);
            $this->accumulateValue($sums, $counts, 'lcp', $bucketIndex, $result->largest_contentful_paint);
            $this->accumulateValue($sums, $counts, 'tbt', $bucketIndex, $result->total_blocking_time);
            $this->accumulateValue($sums, $counts, 'cls', $bucketIndex, $result->cumulative_layout_shift);
        }

        // Store sums/counts temporarily on buckets for finalize step
        $buckets['_sums'] = $sums;
        $buckets['_counts'] = $counts;
    }

    private function accumulateValue(array &$sums, array &$counts, string $key, int $index, $value): void
    {
        if ($value === null) {
            return;
        }

        if (!isset($sums[$key][$index])) {
            $sums[$key][$index] = 0;
            $counts[$key][$index] = 0;
        }

        $sums[$key][$index] += $value;
        $counts[$key][$index] += 1;
    }

    /**
     * Convert sums/counts to averages and return normalized arrays.
     */
    private function finalizeBuckets(array $buckets): array
    {
        $sums = $buckets['_sums'] ?? [];
        $counts = $buckets['_counts'] ?? [];
        unset($buckets['_sums'], $buckets['_counts']);

        foreach ($buckets as $metric => &$values) {
            foreach ($values as $index => &$value) {
                if (isset($sums[$metric][$index]) && $counts[$metric][$index] > 0) {
                    $avg = $sums[$metric][$index] / $counts[$metric][$index];
                    $value = $metric === 'cls' ? round($avg, 3) : round($avg);
                } else {
                    $value = null;
                }
            }
        }

        return $buckets;
    }
}
