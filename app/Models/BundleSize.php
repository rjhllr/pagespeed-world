<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BundleSize extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_id',
        'total_size',
        'total_transfer_size',
        'javascript_size',
        'javascript_transfer_size',
        'css_size',
        'css_transfer_size',
        'image_size',
        'image_transfer_size',
        'font_size',
        'font_transfer_size',
        'html_size',
        'html_transfer_size',
        'other_size',
        'other_transfer_size',
        'total_requests',
        'javascript_requests',
        'css_requests',
        'image_requests',
        'font_requests',
        'dom_content_loaded',
        'load_time',
        'raw_data',
        'status',
        'error_message',
    ];

    protected $casts = [
        'total_size' => 'integer',
        'total_transfer_size' => 'integer',
        'javascript_size' => 'integer',
        'javascript_transfer_size' => 'integer',
        'css_size' => 'integer',
        'css_transfer_size' => 'integer',
        'image_size' => 'integer',
        'image_transfer_size' => 'integer',
        'font_size' => 'integer',
        'font_transfer_size' => 'integer',
        'html_size' => 'integer',
        'html_transfer_size' => 'integer',
        'other_size' => 'integer',
        'other_transfer_size' => 'integer',
        'total_requests' => 'integer',
        'javascript_requests' => 'integer',
        'css_requests' => 'integer',
        'image_requests' => 'integer',
        'font_requests' => 'integer',
        'dom_content_loaded' => 'integer',
        'load_time' => 'integer',
        'raw_data' => 'array',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Format bytes to human-readable size
     */
    public static function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public function getTotalSizeFormatted(): string
    {
        return $this->total_size ? self::formatBytes($this->total_size) : 'N/A';
    }

    public function getJavaScriptSizeFormatted(): string
    {
        return $this->javascript_size ? self::formatBytes($this->javascript_size) : 'N/A';
    }

    public function getCssSizeFormatted(): string
    {
        return $this->css_size ? self::formatBytes($this->css_size) : 'N/A';
    }

    public function getImageSizeFormatted(): string
    {
        return $this->image_size ? self::formatBytes($this->image_size) : 'N/A';
    }

    /**
     * Check if bundle size increased significantly
     */
    public function isAnomaly(float $percentageThreshold = 10.0): bool
    {
        $previousResult = BundleSize::where('page_id', $this->page_id)
            ->where('status', 'success')
            ->where('id', '<', $this->id)
            ->latest()
            ->first();

        if (!$previousResult || !$this->total_size || !$previousResult->total_size) {
            return false;
        }

        $percentageChange = (($this->total_size - $previousResult->total_size) / $previousResult->total_size) * 100;
        
        return abs($percentageChange) >= $percentageThreshold;
    }
}
