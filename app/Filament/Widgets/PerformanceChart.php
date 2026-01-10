<?php

namespace App\Filament\Widgets;

use App\Models\CrawlResult;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PerformanceChart extends ChartWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    public function getHeading(): ?string
    {
        return 'Average Performance Scores (Last 7 Days)';
    }

    protected function getData(): array
    {
        $mobileData = CrawlResult::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('AVG(performance_score) as avg_score')
            )
            ->where('strategy', 'mobile')
            ->where('status', 'success')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('avg_score', 'date')
            ->toArray();

        $desktopData = CrawlResult::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('AVG(performance_score) as avg_score')
            )
            ->where('strategy', 'desktop')
            ->where('status', 'success')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('avg_score', 'date')
            ->toArray();

        $dates = collect(range(6, 0))->map(fn ($days) => now()->subDays($days)->format('Y-m-d'));

        return [
            'datasets' => [
                [
                    'label' => 'Mobile',
                    'data' => $dates->map(fn ($date) => round($mobileData[$date] ?? 0, 1))->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                ],
                [
                    'label' => 'Desktop',
                    'data' => $dates->map(fn ($date) => round($desktopData[$date] ?? 0, 1))->toArray(),
                    'backgroundColor' => 'rgba(16, 185, 129, 0.5)',
                    'borderColor' => 'rgb(16, 185, 129)',
                ],
            ],
            'labels' => $dates->map(fn ($date) => \Carbon\Carbon::parse($date)->format('M d'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
        ];
    }
}
