<?php

namespace App\Jobs;

use App\Models\Page;
use App\Models\Report;
use App\Services\BundleSizeService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class AnalyzeBundleSizeJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;
    public int $backoff = 60;
    public int $timeout = 180;

    public function __construct(
        public Page $page
    ) {}

    public function handle(BundleSizeService $service): void
    {
        if (!$this->page->is_active) {
            Log::info('Skipping bundle analysis for inactive page', ['page_id' => $this->page->id]);
            return;
        }

        $result = $service->analyze($this->page);

        if ($result && $result->status === 'success') {
            // Check for significant bundle size increase
            if ($result->isAnomaly(15.0)) {
                $this->createAnomalyReport($result);
            }
        }
    }

    private function createAnomalyReport($result): void
    {
        $previousResult = $this->page->bundleSizes()
            ->where('status', 'success')
            ->where('id', '<', $result->id)
            ->latest()
            ->first();

        if (!$previousResult || !$previousResult->total_size) {
            return;
        }

        $change = $result->total_size - $previousResult->total_size;
        $percentChange = round(($change / $previousResult->total_size) * 100, 1);

        $direction = $change > 0 ? 'increased' : 'decreased';
        $changeText = $change > 0 ? "+{$percentChange}%" : "{$percentChange}%";

        Report::create([
            'organization_id' => $this->page->organization_id,
            'page_id' => $this->page->id,
            'type' => 'anomaly',
            'subject' => "Bundle size {$direction}: {$this->page->name}",
            'content' => "Significant bundle size change detected for {$this->page->name}.\n\n" .
                "URL: {$this->page->url}\n" .
                "Previous Size: " . \App\Models\BundleSize::formatBytes($previousResult->total_size) . "\n" .
                "Current Size: " . \App\Models\BundleSize::formatBytes($result->total_size) . "\n" .
                "Change: {$changeText}\n\n" .
                "JavaScript: " . \App\Models\BundleSize::formatBytes($result->javascript_size ?? 0) . "\n" .
                "CSS: " . \App\Models\BundleSize::formatBytes($result->css_size ?? 0) . "\n" .
                "Images: " . \App\Models\BundleSize::formatBytes($result->image_size ?? 0),
            'data' => [
                'previous_size' => $previousResult->total_size,
                'current_size' => $result->total_size,
                'change_bytes' => $change,
                'change_percent' => $percentChange,
                'bundle_size_id' => $result->id,
            ],
            'status' => 'pending',
            'recipients' => $this->page->organization->users->pluck('email')->toArray(),
        ]);

        Log::info('Bundle size anomaly report created', [
            'page_id' => $this->page->id,
            'change_percent' => $percentChange,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Bundle size analysis job failed', [
            'page_id' => $this->page->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
