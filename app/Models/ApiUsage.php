<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ApiUsage extends Model
{
    protected $table = 'api_usage';

    protected $fillable = [
        'date',
        'requests_count',
        'daily_quota',
    ];

    protected $casts = [
        'date' => 'date',
        'requests_count' => 'integer',
        'daily_quota' => 'integer',
    ];

    public static function getTodayUsage(): self
    {
        return self::firstOrCreate(
            ['date' => now()->toDateString()],
            [
                'requests_count' => 0,
                'daily_quota' => config('services.psi.daily_quota', 25000),
            ]
        );
    }

    public static function incrementUsage(): void
    {
        $usage = self::getTodayUsage();
        $usage->increment('requests_count');
        
        // Clear the cached remaining quota
        Cache::forget('psi_remaining_quota_' . now()->toDateString());
    }

    public static function canMakeRequest(): bool
    {
        $usage = self::getTodayUsage();
        
        // Leave some buffer (1% of quota)
        $buffer = (int) ($usage->daily_quota * 0.01);
        
        return $usage->requests_count < ($usage->daily_quota - $buffer);
    }

    public static function getRemainingQuota(): int
    {
        $cacheKey = 'psi_remaining_quota_' . now()->toDateString();
        
        return Cache::remember($cacheKey, 60, function () {
            $usage = self::getTodayUsage();
            return max(0, $usage->daily_quota - $usage->requests_count);
        });
    }

    public static function getUsagePercentage(): float
    {
        $usage = self::getTodayUsage();
        
        if ($usage->daily_quota === 0) {
            return 100;
        }
        
        return round(($usage->requests_count / $usage->daily_quota) * 100, 2);
    }
}
