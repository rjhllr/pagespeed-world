<?php

namespace App\Filament\Widgets;

use App\Models\ApiUsage;
use App\Models\CrawlResult;
use App\Models\Organization;
use App\Models\Page;
use App\Models\Report;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $quotaStatus = ApiUsage::getTodayUsage();
        $remainingQuota = ApiUsage::getRemainingQuota();
        $quotaPercentage = ApiUsage::getUsagePercentage();

        return [
            Stat::make('Organizations', Organization::where('is_active', true)->count())
                ->description('Active organizations')
                ->icon('heroicon-o-building-office')
                ->color('primary'),
                
            Stat::make('Pages Monitored', Page::where('is_active', true)->count())
                ->description('Active pages')
                ->icon('heroicon-o-globe-alt')
                ->color('success'),
                
            Stat::make('Crawls Today', CrawlResult::whereDate('created_at', today())->count())
                ->description('Mobile + Desktop')
                ->icon('heroicon-o-chart-bar')
                ->color('info'),
                
            Stat::make('Pending Reports', Report::where('status', 'pending')->count())
                ->description('Awaiting review')
                ->icon('heroicon-o-document-text')
                ->color('warning'),
                
            Stat::make('API Quota Used', $quotaStatus->requests_count . ' / ' . $quotaStatus->daily_quota)
                ->description(round($quotaPercentage, 1) . '% used today')
                ->icon('heroicon-o-signal')
                ->color($quotaPercentage > 80 ? 'danger' : ($quotaPercentage > 50 ? 'warning' : 'success')),
                
            Stat::make('Remaining Quota', number_format($remainingQuota))
                ->description('Requests left today')
                ->icon('heroicon-o-battery-100')
                ->color($remainingQuota < 1000 ? 'danger' : 'success'),
        ];
    }
}
