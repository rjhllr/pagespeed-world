<?php

namespace App\Http\Controllers;

use App\Models\BundleSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ScreenshotController extends Controller
{
    /**
     * Stream a filmstrip screenshot through authenticated controller.
     * 
     * Route: /screenshots/{bundleSize}/{filename}
     */
    public function show(Request $request, BundleSize $bundleSize, string $filename): StreamedResponse
    {
        // Verify the filmstrip exists and contains this file
        $filmstrip = $bundleSize->filmstrip ?? [];
        $frame = collect($filmstrip)->first(fn ($f) => str_ends_with($f['path'] ?? '', $filename));
        
        if (!$frame) {
            abort(404, 'Screenshot not found');
        }

        $disk = config('services.screenshots.disk', 'public');
        $path = $frame['path'];

        if (!Storage::disk($disk)->exists($path)) {
            abort(404, 'Screenshot file not found');
        }

        $mimeType = 'image/webp';
        
        return response()->stream(
            function () use ($disk, $path) {
                $stream = Storage::disk($disk)->readStream($path);
                fpassthru($stream);
                if (is_resource($stream)) {
                    fclose($stream);
                }
            },
            200,
            [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
                'Cache-Control' => 'private, max-age=3600',
            ]
        );
    }
}
