<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'name',
        'url',
        'description',
        'is_active',
        'crawl_interval_hours',
        'last_crawled_at',
        'next_crawl_at',
        'filmstrip_retention_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'crawl_interval_hours' => 'integer',
        'last_crawled_at' => 'datetime',
        'next_crawl_at' => 'datetime',
        'filmstrip_retention_count' => 'integer',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function crawlResults(): HasMany
    {
        return $this->hasMany(CrawlResult::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function bundleSizes(): HasMany
    {
        return $this->hasMany(BundleSize::class);
    }

    public function latestBundleSize()
    {
        return $this->hasOne(BundleSize::class)
            ->where('status', 'success')
            ->latest();
    }

    public function latestMobileResult()
    {
        return $this->hasOne(CrawlResult::class)
            ->where('strategy', 'mobile')
            ->where('status', 'success')
            ->latest();
    }

    public function latestDesktopResult()
    {
        return $this->hasOne(CrawlResult::class)
            ->where('strategy', 'desktop')
            ->where('status', 'success')
            ->latest();
    }

    public function needsCrawl(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if (!$this->next_crawl_at) {
            return true;
        }

        return $this->next_crawl_at->isPast();
    }

    public function updateNextCrawlTime(): void
    {
        $this->update([
            'last_crawled_at' => now(),
            'next_crawl_at' => now()->addHours($this->crawl_interval_hours),
        ]);
    }

    public function getAveragePerformanceScore(string $strategy = 'mobile', int $days = 7): ?float
    {
        return $this->crawlResults()
            ->where('strategy', $strategy)
            ->where('status', 'success')
            ->where('created_at', '>=', now()->subDays($days))
            ->avg('performance_score');
    }

    /**
     * Get the effective filmstrip retention count for this page.
     * Uses page setting if set, otherwise falls back to organization max.
     * Always capped by organization max.
     */
    public function getEffectiveFilmstripRetention(): int
    {
        $orgMax = $this->organization->max_filmstrip_retention ?? 10;
        
        if ($this->filmstrip_retention_count !== null) {
            return min($this->filmstrip_retention_count, $orgMax);
        }
        
        return $orgMax;
    }
}
