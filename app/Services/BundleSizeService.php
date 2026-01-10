<?php

namespace App\Services;

use App\Models\BundleSize;
use App\Models\Page;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class BundleSizeService
{
    private string $scriptPath;
    private int $timeout;

    public function __construct()
    {
        $this->scriptPath = base_path('scripts/analyze-bundle.js');
        $this->timeout = config('services.bundle_analyzer.timeout', 120);
    }

    public function analyze(Page $page): ?BundleSize
    {
        // Create pending result
        $bundleSize = BundleSize::create([
            'page_id' => $page->id,
            'status' => 'pending',
        ]);

        try {
            // Run the Node.js script
            $result = Process::timeout($this->timeout)
                ->run(['node', $this->scriptPath, $page->url]);

            if (!$result->successful()) {
                $bundleSize->update([
                    'status' => 'error',
                    'error_message' => $result->errorOutput() ?: 'Process failed with exit code: ' . $result->exitCode(),
                ]);

                Log::error('Bundle size analysis failed', [
                    'page_id' => $page->id,
                    'error' => $result->errorOutput(),
                    'exit_code' => $result->exitCode(),
                ]);

                return $bundleSize;
            }

            $output = $result->output();
            $data = json_decode($output, true);

            if (!$data || !isset($data['success']) || !$data['success']) {
                $errorMessage = $data['error'] ?? 'Failed to parse analyzer output';
                $bundleSize->update([
                    'status' => 'error',
                    'error_message' => $errorMessage,
                ]);

                Log::error('Bundle size analysis returned error', [
                    'page_id' => $page->id,
                    'error' => $errorMessage,
                ]);

                return $bundleSize;
            }

            // Extract data from response
            $breakdown = $data['breakdown'] ?? [];
            $totals = $data['totals'] ?? [];
            $timing = $data['timing'] ?? [];

            $bundleSize->update([
                'status' => 'success',
                'total_size' => $totals['size'] ?? null,
                'total_transfer_size' => $totals['transferSize'] ?? null,
                'javascript_size' => $breakdown['javascript']['size'] ?? null,
                'javascript_transfer_size' => $breakdown['javascript']['transferSize'] ?? null,
                'javascript_download_time' => $breakdown['javascript']['downloadTime'] ?? null,
                'css_size' => $breakdown['css']['size'] ?? null,
                'css_transfer_size' => $breakdown['css']['transferSize'] ?? null,
                'css_download_time' => $breakdown['css']['downloadTime'] ?? null,
                'image_size' => $breakdown['images']['size'] ?? null,
                'image_transfer_size' => $breakdown['images']['transferSize'] ?? null,
                'image_download_time' => $breakdown['images']['downloadTime'] ?? null,
                'font_size' => $breakdown['fonts']['size'] ?? null,
                'font_transfer_size' => $breakdown['fonts']['transferSize'] ?? null,
                'font_download_time' => $breakdown['fonts']['downloadTime'] ?? null,
                'html_size' => $breakdown['html']['size'] ?? null,
                'html_transfer_size' => $breakdown['html']['transferSize'] ?? null,
                'html_download_time' => $breakdown['html']['downloadTime'] ?? null,
                'other_size' => $breakdown['other']['size'] ?? null,
                'other_transfer_size' => $breakdown['other']['transferSize'] ?? null,
                'total_requests' => $totals['requests'] ?? null,
                'javascript_requests' => $breakdown['javascript']['requests'] ?? null,
                'css_requests' => $breakdown['css']['requests'] ?? null,
                'image_requests' => $breakdown['images']['requests'] ?? null,
                'font_requests' => $breakdown['fonts']['requests'] ?? null,
                'dom_content_loaded' => $timing['domContentLoaded'] ?? null,
                'load_time' => $timing['loadTime'] ?? null,
                'total_download_time' => $totals['downloadTime'] ?? null,
                'slow_request_count' => $totals['slowRequestCount'] ?? null,
                'compression_ratio' => $totals['compressionRatio'] ?? null,
                'raw_data' => $data,
            ]);

            Log::info('Bundle size analysis completed', [
                'page_id' => $page->id,
                'total_size' => $bundleSize->total_size,
                'js_size' => $bundleSize->javascript_size,
            ]);

            return $bundleSize;

        } catch (\Exception $e) {
            $bundleSize->update([
                'status' => 'error',
                'error_message' => $e->getMessage(),
            ]);

            Log::error('Bundle size analysis exception', [
                'page_id' => $page->id,
                'error' => $e->getMessage(),
            ]);

            return $bundleSize;
        }
    }
}
