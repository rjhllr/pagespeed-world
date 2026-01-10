<?php

namespace App\Jobs;

use App\Models\ApiUsage;
use App\Models\Page;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ScheduledCrawlJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        // Check if we have quota left
        if (!ApiUsage::canMakeRequest()) {
            Log::warning('Scheduled crawl skipped - daily quota exceeded');
            return;
        }

        $pages = Page::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('next_crawl_at')
                    ->orWhere('next_crawl_at', '<=', now());
            })
            ->whereHas('organization', function ($query) {
                $query->where('is_active', true);
            })
            ->orderBy('next_crawl_at', 'asc')
            ->limit(50) // Process in batches
            ->get();

        Log::info('Scheduled crawl starting', ['pages_count' => $pages->count()]);

        foreach ($pages as $page) {
            if (!ApiUsage::canMakeRequest()) {
                Log::warning('Scheduled crawl stopped - quota limit reached');
                break;
            }

            // Dispatch jobs for both mobile and desktop
            AnalyzePageJob::dispatch($page, 'mobile')->onQueue('psi');
            AnalyzePageJob::dispatch($page, 'desktop')->onQueue('psi');

            // Update next crawl time
            $page->updateNextCrawlTime();
        }

        Log::info('Scheduled crawl jobs dispatched');
    }
}
