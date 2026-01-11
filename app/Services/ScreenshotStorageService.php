<?php

namespace App\Services;

use App\Models\BundleSize;
use App\Models\Page;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ScreenshotStorageService
{
    private string $disk;
    private string $basePath = 'screenshots/bundle-sizes';

    public function __construct()
    {
        $this->disk = config('services.screenshots.disk', 'public');
    }

    /**
     * Store filmstrip screenshots from base64 data
     * 
     * @param BundleSize $bundleSize
     * @param array $filmstripData Array of frames with timestamp, event, and base64 image
     * @return array Stored filmstrip data with paths instead of base64
     */
    public function storeFilmstrip(BundleSize $bundleSize, array $filmstripData): array
    {
        $storedFrames = [];
        $directory = "{$this->basePath}/{$bundleSize->id}";

        foreach ($filmstripData as $frame) {
            if (empty($frame['image'])) {
                continue;
            }

            $timestamp = $frame['timestamp'] ?? 0;
            $event = $frame['event'] ?? null;
            $filename = "{$timestamp}.webp";
            $path = "{$directory}/{$filename}";

            try {
                // Decode base64 and store
                $imageData = base64_decode($frame['image']);
                Storage::disk($this->disk)->put($path, $imageData, ['visibility' => 'private']);

                $storedFrames[] = [
                    'timestamp' => $timestamp,
                    'event' => $event,
                    'path' => $path,
                ];
            } catch (\Exception $e) {
                Log::warning('Failed to store filmstrip screenshot', [
                    'bundle_size_id' => $bundleSize->id,
                    'timestamp' => $timestamp,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $storedFrames;
    }

    /**
     * Get URLs for filmstrip screenshots
     * 
     * @param int $bundleSizeId The bundle size ID for URL generation
     * @param array $filmstrip Stored filmstrip data with paths
     * @return array Filmstrip data with URLs instead of paths
     */
    public function getFilmstripUrls(int $bundleSizeId, array $filmstrip): array
    {
        return array_map(function ($frame) use ($bundleSizeId) {
            $path = $frame['path'] ?? '';
            $filename = basename($path);
            
            return [
                'timestamp' => $frame['timestamp'] ?? 0,
                'event' => $frame['event'] ?? null,
                'url' => $filename ? route('screenshots.show', [
                    'bundleSize' => $bundleSizeId,
                    'filename' => $filename,
                ]) : null,
            ];
        }, $filmstrip);
    }

    /**
     * Delete filmstrip screenshots for a bundle size
     */
    public function deleteFilmstrip(BundleSize $bundleSize): void
    {
        $directory = "{$this->basePath}/{$bundleSize->id}";
        
        try {
            Storage::disk($this->disk)->deleteDirectory($directory);
        } catch (\Exception $e) {
            Log::warning('Failed to delete filmstrip directory', [
                'bundle_size_id' => $bundleSize->id,
                'directory' => $directory,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Clean up old screenshots beyond retention limit for a page
     */
    public function cleanupOldScreenshots(Page $page): void
    {
        $retentionLimit = $page->getEffectiveFilmstripRetention();
        
        // Get bundle sizes with filmstrips, ordered by newest first
        $bundleSizesWithFilmstrips = $page->bundleSizes()
            ->whereNotNull('filmstrip')
            ->where('filmstrip', '!=', '[]')
            ->orderByDesc('created_at')
            ->get();
        
        // Skip the ones within retention limit
        $toCleanup = $bundleSizesWithFilmstrips->skip($retentionLimit);
        
        foreach ($toCleanup as $bundleSize) {
            $this->deleteFilmstrip($bundleSize);
            $bundleSize->update(['filmstrip' => null]);
            
            Log::info('Cleaned up filmstrip screenshots', [
                'bundle_size_id' => $bundleSize->id,
                'page_id' => $page->id,
            ]);
        }
    }
}
