<?php

namespace App\Services;

use App\Models\ApiUsage;
use App\Models\CrawlResult;
use App\Models\Page;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class PageSpeedInsightsService
{
    private string $apiKey;
    private string $baseUrl;
    private int $requestsPerMinute;

    public function __construct()
    {
        $this->apiKey = config('services.psi.api_key');
        $this->baseUrl = config('services.psi.base_url');
        $this->requestsPerMinute = config('services.psi.requests_per_minute', 400);
    }

    public function analyze(Page $page, string $strategy = 'mobile'): ?CrawlResult
    {
        // Check daily quota
        if (!ApiUsage::canMakeRequest()) {
            Log::warning('PSI daily quota exceeded', [
                'page_id' => $page->id,
                'remaining' => ApiUsage::getRemainingQuota(),
            ]);
            return null;
        }

        // Rate limit per minute
        $rateLimitKey = 'psi_rate_limit';
        if (RateLimiter::tooManyAttempts($rateLimitKey, $this->requestsPerMinute)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            Log::info("PSI rate limit hit, waiting {$seconds}s", ['page_id' => $page->id]);
            sleep($seconds);
        }

        RateLimiter::hit($rateLimitKey, 60);

        // Create pending result
        $result = CrawlResult::create([
            'page_id' => $page->id,
            'strategy' => $strategy,
            'status' => 'pending',
        ]);

        try {
            // Build URL with multiple category params (PSI API requires repeated category params)
            $url = $this->baseUrl . '?' . http_build_query([
                'url' => $page->url,
                'key' => $this->apiKey,
                'strategy' => $strategy,
            ]) . '&category=performance&category=accessibility&category=best-practices&category=seo';

            $response = Http::timeout(120)->get($url);

            // Increment usage
            ApiUsage::incrementUsage();

            if ($response->failed()) {
                $result->update([
                    'status' => 'error',
                    'error_message' => $response->body(),
                ]);
                
                Log::error('PSI API error', [
                    'page_id' => $page->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                
                return $result;
            }

            $data = $response->json();
            $lighthouseResult = $data['lighthouseResult'] ?? [];
            $categories = $lighthouseResult['categories'] ?? [];
            $audits = $lighthouseResult['audits'] ?? [];

            $result->update([
                'status' => 'success',
                'performance_score' => isset($categories['performance']['score']) 
                    ? $categories['performance']['score'] * 100 
                    : null,
                'accessibility_score' => isset($categories['accessibility']['score']) 
                    ? $categories['accessibility']['score'] * 100 
                    : null,
                'best_practices_score' => isset($categories['best-practices']['score']) 
                    ? $categories['best-practices']['score'] * 100 
                    : null,
                'seo_score' => isset($categories['seo']['score']) 
                    ? $categories['seo']['score'] * 100 
                    : null,
                'first_contentful_paint' => $audits['first-contentful-paint']['numericValue'] ?? null,
                'largest_contentful_paint' => $audits['largest-contentful-paint']['numericValue'] ?? null,
                'total_blocking_time' => $audits['total-blocking-time']['numericValue'] ?? null,
                'cumulative_layout_shift' => $audits['cumulative-layout-shift']['numericValue'] ?? null,
                'speed_index' => $audits['speed-index']['numericValue'] ?? null,
                'time_to_interactive' => $audits['interactive']['numericValue'] ?? null,
                'raw_response' => $data,
            ]);

            // Update page's last_crawled_at timestamp
            $page->update(['last_crawled_at' => now()]);

            Log::info('PSI analysis completed', [
                'page_id' => $page->id,
                'strategy' => $strategy,
                'performance' => $result->performance_score,
            ]);

            return $result;

        } catch (\Exception $e) {
            $result->update([
                'status' => 'error',
                'error_message' => $e->getMessage(),
            ]);

            Log::error('PSI analysis failed', [
                'page_id' => $page->id,
                'error' => $e->getMessage(),
            ]);

            return $result;
        }
    }

    public function analyzeAll(Page $page): array
    {
        return [
            'mobile' => $this->analyze($page, 'mobile'),
            'desktop' => $this->analyze($page, 'desktop'),
        ];
    }

    public function getQuotaStatus(): array
    {
        $usage = ApiUsage::getTodayUsage();
        
        return [
            'used' => $usage->requests_count,
            'quota' => $usage->daily_quota,
            'remaining' => ApiUsage::getRemainingQuota(),
            'percentage' => ApiUsage::getUsagePercentage(),
            'can_make_request' => ApiUsage::canMakeRequest(),
        ];
    }
}
