<?php

namespace App\Jobs;

use App\Models\Page;
use App\Models\Report;
use App\Services\PageSpeedInsightsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Support\Facades\Log;

class AnalyzePageJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public Page $page,
        public string $strategy = 'mobile'
    ) {}

    public function middleware(): array
    {
        return [new RateLimited('psi')];
    }

    public function handle(PageSpeedInsightsService $service): void
    {
        if (!$this->page->is_active) {
            Log::info('Skipping inactive page', ['page_id' => $this->page->id]);
            return;
        }

        $result = $service->analyze($this->page, $this->strategy);

        if ($result && $result->status === 'success') {
            // Check for anomalies
            if ($result->isAnomaly(10.0)) {
                $this->createAnomalyReport($result);
            }
        }
    }

    private function createAnomalyReport($result): void
    {
        $previousResult = $this->page->crawlResults()
            ->where('strategy', $this->strategy)
            ->where('status', 'success')
            ->where('id', '<', $result->id)
            ->latest()
            ->first();

        $change = $previousResult 
            ? round($result->performance_score - $previousResult->performance_score, 1)
            : 0;

        $changeText = $change > 0 ? "+{$change}" : $change;
        $direction = $change > 0 ? 'improved' : 'degraded';

        Report::create([
            'organization_id' => $this->page->organization_id,
            'page_id' => $this->page->id,
            'type' => 'anomaly',
            'subject' => "Performance {$direction}: {$this->page->name} ({$this->strategy})",
            'content' => "Significant performance change detected for {$this->page->name}.\n\n" .
                "URL: {$this->page->url}\n" .
                "Strategy: " . ucfirst($this->strategy) . "\n" .
                "Previous Score: " . ($previousResult->performance_score ?? 'N/A') . "\n" .
                "Current Score: {$result->performance_score}\n" .
                "Change: {$changeText} points",
            'data' => [
                'previous_score' => $previousResult->performance_score ?? null,
                'current_score' => $result->performance_score,
                'change' => $change,
                'strategy' => $this->strategy,
                'crawl_result_id' => $result->id,
            ],
            'status' => 'pending',
            'recipients' => $this->page->organization->users->pluck('email')->toArray(),
        ]);

        Log::info('Anomaly report created', [
            'page_id' => $this->page->id,
            'change' => $change,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Page analysis job failed', [
            'page_id' => $this->page->id,
            'strategy' => $this->strategy,
            'error' => $exception->getMessage(),
        ]);
    }
}
