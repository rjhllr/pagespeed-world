<?php

namespace App\Jobs;

use App\Models\Organization;
use App\Models\Report;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GenerateWeeklyReportsJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $organizations = Organization::where('is_active', true)
            ->with(['pages' => function ($query) {
                $query->where('is_active', true);
            }])
            ->get();

        foreach ($organizations as $organization) {
            $this->generateReport($organization);
        }

        Log::info('Weekly reports generated', ['organizations' => $organizations->count()]);
    }

    private function generateReport(Organization $organization): void
    {
        if ($organization->pages->isEmpty()) {
            return;
        }

        $reportData = [
            'period_start' => now()->subWeek()->toDateString(),
            'period_end' => now()->toDateString(),
            'pages' => [],
        ];

        foreach ($organization->pages as $page) {
            $mobileResults = $page->crawlResults()
                ->where('strategy', 'mobile')
                ->where('status', 'success')
                ->where('created_at', '>=', now()->subWeek())
                ->orderBy('created_at')
                ->get();

            $desktopResults = $page->crawlResults()
                ->where('strategy', 'desktop')
                ->where('status', 'success')
                ->where('created_at', '>=', now()->subWeek())
                ->orderBy('created_at')
                ->get();

            $pageData = [
                'id' => $page->id,
                'name' => $page->name,
                'url' => $page->url,
                'mobile' => $this->calculateMetrics($mobileResults),
                'desktop' => $this->calculateMetrics($desktopResults),
            ];

            $reportData['pages'][] = $pageData;
        }

        $content = $this->generateContent($organization, $reportData);

        Report::create([
            'organization_id' => $organization->id,
            'type' => 'weekly',
            'subject' => "Weekly Performance Report - {$organization->name}",
            'content' => $content,
            'data' => $reportData,
            'status' => 'pending',
            'recipients' => $organization->users->pluck('email')->toArray(),
        ]);
    }

    private function calculateMetrics($results): array
    {
        if ($results->isEmpty()) {
            return [
                'avg_performance' => null,
                'avg_accessibility' => null,
                'avg_best_practices' => null,
                'avg_seo' => null,
                'trend' => null,
                'crawl_count' => 0,
            ];
        }

        $first = $results->first();
        $last = $results->last();
        $trend = null;

        if ($first && $last && $first->performance_score && $last->performance_score) {
            $trend = round($last->performance_score - $first->performance_score, 1);
        }

        return [
            'avg_performance' => round($results->avg('performance_score'), 1),
            'avg_accessibility' => round($results->avg('accessibility_score'), 1),
            'avg_best_practices' => round($results->avg('best_practices_score'), 1),
            'avg_seo' => round($results->avg('seo_score'), 1),
            'trend' => $trend,
            'crawl_count' => $results->count(),
        ];
    }

    private function generateContent(Organization $organization, array $data): string
    {
        $content = "# Weekly Performance Report\n\n";
        $content .= "**Organization:** {$organization->name}\n";
        $content .= "**Period:** {$data['period_start']} to {$data['period_end']}\n\n";
        $content .= "## Summary\n\n";

        foreach ($data['pages'] as $page) {
            $content .= "### {$page['name']}\n";
            $content .= "URL: {$page['url']}\n\n";

            $content .= "**Mobile:**\n";
            if ($page['mobile']['avg_performance']) {
                $content .= "- Performance: {$page['mobile']['avg_performance']}";
                if ($page['mobile']['trend'] !== null) {
                    $trendSymbol = $page['mobile']['trend'] >= 0 ? '↑' : '↓';
                    $content .= " ({$trendSymbol} {$page['mobile']['trend']})";
                }
                $content .= "\n";
                $content .= "- Accessibility: {$page['mobile']['avg_accessibility']}\n";
                $content .= "- Best Practices: {$page['mobile']['avg_best_practices']}\n";
                $content .= "- SEO: {$page['mobile']['avg_seo']}\n";
            } else {
                $content .= "- No data available\n";
            }

            $content .= "\n**Desktop:**\n";
            if ($page['desktop']['avg_performance']) {
                $content .= "- Performance: {$page['desktop']['avg_performance']}";
                if ($page['desktop']['trend'] !== null) {
                    $trendSymbol = $page['desktop']['trend'] >= 0 ? '↑' : '↓';
                    $content .= " ({$trendSymbol} {$page['desktop']['trend']})";
                }
                $content .= "\n";
                $content .= "- Accessibility: {$page['desktop']['avg_accessibility']}\n";
                $content .= "- Best Practices: {$page['desktop']['avg_best_practices']}\n";
                $content .= "- SEO: {$page['desktop']['avg_seo']}\n";
            } else {
                $content .= "- No data available\n";
            }

            $content .= "\n---\n\n";
        }

        return $content;
    }
}
