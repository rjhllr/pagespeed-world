<?php

namespace App\Http\Controllers;

use App\Models\CrawlResult;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $pages = Page::where('organization_id', $user->organization_id)
            ->where('is_active', true)
            ->with(['latestMobileResult', 'latestDesktopResult'])
            ->get();

        return view('dashboard.index', compact('pages'));
    }

    public function show(Page $page)
    {
        $user = Auth::user();
        
        // Ensure user can only view their organization's pages
        if (!$user->is_admin && $page->organization_id !== $user->organization_id) {
            abort(403);
        }

        $page->load(['organization', 'latestMobileResult', 'latestDesktopResult']);

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

        // Prepare chart data
        $chartData = $this->prepareChartData($mobileResults, $desktopResults);

        return view('dashboard.show', compact('page', 'mobileResults', 'desktopResults', 'chartData'));
    }

    public function pageMetrics(Page $page)
    {
        $user = Auth::user();
        
        if (!$user->is_admin && $page->organization_id !== $user->organization_id) {
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

    private function prepareChartData($mobileResults, $desktopResults)
    {
        return [
            'performance' => [
                'mobile' => [
                    'dates' => $mobileResults->pluck('created_at')->map(fn ($d) => $d->format('M d H:i'))->toArray(),
                    'scores' => $mobileResults->pluck('performance_score')->map(fn ($s) => round($s, 1))->toArray(),
                ],
                'desktop' => [
                    'dates' => $desktopResults->pluck('created_at')->map(fn ($d) => $d->format('M d H:i'))->toArray(),
                    'scores' => $desktopResults->pluck('performance_score')->map(fn ($s) => round($s, 1))->toArray(),
                ],
            ],
            'webVitals' => [
                'mobile' => [
                    'fcp' => $mobileResults->pluck('first_contentful_paint')->map(fn ($v) => $v ? round($v) : null)->toArray(),
                    'lcp' => $mobileResults->pluck('largest_contentful_paint')->map(fn ($v) => $v ? round($v) : null)->toArray(),
                    'tbt' => $mobileResults->pluck('total_blocking_time')->map(fn ($v) => $v ? round($v) : null)->toArray(),
                    'cls' => $mobileResults->pluck('cumulative_layout_shift')->map(fn ($v) => $v ? round($v, 3) : null)->toArray(),
                    'dates' => $mobileResults->pluck('created_at')->map(fn ($d) => $d->format('M d H:i'))->toArray(),
                ],
                'desktop' => [
                    'fcp' => $desktopResults->pluck('first_contentful_paint')->map(fn ($v) => $v ? round($v) : null)->toArray(),
                    'lcp' => $desktopResults->pluck('largest_contentful_paint')->map(fn ($v) => $v ? round($v) : null)->toArray(),
                    'tbt' => $desktopResults->pluck('total_blocking_time')->map(fn ($v) => $v ? round($v) : null)->toArray(),
                    'cls' => $desktopResults->pluck('cumulative_layout_shift')->map(fn ($v) => $v ? round($v, 3) : null)->toArray(),
                    'dates' => $desktopResults->pluck('created_at')->map(fn ($d) => $d->format('M d H:i'))->toArray(),
                ],
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
    }
}
