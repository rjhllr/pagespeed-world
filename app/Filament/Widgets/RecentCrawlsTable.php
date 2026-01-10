<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\CrawlResultResource;
use App\Models\CrawlResult;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentCrawlsTable extends BaseWidget
{
    protected static ?string $heading = 'Recent Crawls';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                CrawlResult::query()
                    ->with(['page.organization'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('page.name')
                    ->label('Page')
                    ->searchable(),
                TextColumn::make('page.organization.name')
                    ->label('Organization'),
                TextColumn::make('strategy')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'mobile' => 'primary',
                        'desktop' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'success' => 'success',
                        'error' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('performance_score')
                    ->label('Performance')
                    ->formatStateUsing(fn ($state) => $state ? round($state) : '-')
                    ->color(fn ($state) => match(true) {
                        $state >= 90 => 'success',
                        $state >= 50 => 'warning',
                        $state > 0 => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordUrl(fn (CrawlResult $record): string => CrawlResultResource::getUrl('view', ['record' => $record]))
            ->paginated(false);
    }
}
