<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrawlResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_id',
        'strategy',
        'performance_score',
        'accessibility_score',
        'best_practices_score',
        'seo_score',
        'first_contentful_paint',
        'largest_contentful_paint',
        'total_blocking_time',
        'cumulative_layout_shift',
        'speed_index',
        'time_to_interactive',
        'raw_response',
        'status',
        'error_message',
    ];

    protected $casts = [
        'performance_score' => 'float',
        'accessibility_score' => 'float',
        'best_practices_score' => 'float',
        'seo_score' => 'float',
        'first_contentful_paint' => 'float',
        'largest_contentful_paint' => 'float',
        'total_blocking_time' => 'float',
        'cumulative_layout_shift' => 'float',
        'speed_index' => 'float',
        'time_to_interactive' => 'float',
        'raw_response' => 'array',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function getPerformanceGrade(): string
    {
        $score = $this->performance_score;
        
        if ($score === null) {
            return 'N/A';
        }
        
        if ($score >= 90) {
            return 'A';
        } elseif ($score >= 50) {
            return 'B';
        } else {
            return 'C';
        }
    }

    public function getPerformanceColor(): string
    {
        $score = $this->performance_score;
        
        if ($score === null) {
            return 'gray';
        }
        
        if ($score >= 90) {
            return 'success';
        } elseif ($score >= 50) {
            return 'warning';
        } else {
            return 'danger';
        }
    }

    public function isAnomaly(float $threshold = 10.0): bool
    {
        $previousResult = CrawlResult::where('page_id', $this->page_id)
            ->where('strategy', $this->strategy)
            ->where('status', 'success')
            ->where('id', '<', $this->id)
            ->latest()
            ->first();

        if (!$previousResult || !$this->performance_score || !$previousResult->performance_score) {
            return false;
        }

        $difference = abs($this->performance_score - $previousResult->performance_score);
        
        return $difference >= $threshold;
    }
}
