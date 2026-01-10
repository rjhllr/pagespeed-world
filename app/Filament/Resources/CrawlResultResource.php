<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CrawlResultResource\Pages;
use App\Models\CrawlResult;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CrawlResultResource extends Resource
{
    protected static ?string $model = CrawlResult::class;
    protected static ?string $navigationLabel = 'Crawl Results';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-chart-bar';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Monitoring';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function form(Schema $form): Schema
    {
        return $form
            ->components([
                Section::make('Result Details')
                    ->schema([
                        Select::make('page_id')
                            ->relationship('page', 'name')
                            ->required()
                            ->disabled(),
                        Select::make('strategy')
                            ->options([
                                'mobile' => 'Mobile',
                                'desktop' => 'Desktop',
                            ])
                            ->disabled(),
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'success' => 'Success',
                                'error' => 'Error',
                            ])
                            ->disabled(),
                    ])->columns(3),

                Section::make('Scores')
                    ->schema([
                        TextInput::make('performance_score')
                            ->label('Performance')
                            ->disabled(),
                        TextInput::make('accessibility_score')
                            ->label('Accessibility')
                            ->disabled(),
                        TextInput::make('best_practices_score')
                            ->label('Best Practices')
                            ->disabled(),
                        TextInput::make('seo_score')
                            ->label('SEO')
                            ->disabled(),
                    ])->columns(4),

                Section::make('Metrics')
                    ->schema([
                        TextInput::make('first_contentful_paint')
                            ->label('FCP (ms)')
                            ->disabled(),
                        TextInput::make('largest_contentful_paint')
                            ->label('LCP (ms)')
                            ->disabled(),
                        TextInput::make('total_blocking_time')
                            ->label('TBT (ms)')
                            ->disabled(),
                        TextInput::make('cumulative_layout_shift')
                            ->label('CLS')
                            ->disabled(),
                        TextInput::make('speed_index')
                            ->label('Speed Index (ms)')
                            ->disabled(),
                        TextInput::make('time_to_interactive')
                            ->label('TTI (ms)')
                            ->disabled(),
                    ])->columns(3),

                Section::make('Error Details')
                    ->schema([
                        Textarea::make('error_message')
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (?CrawlResult $record): bool => $record?->status === 'error'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('page.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('page.organization.name')
                    ->label('Organization')
                    ->sortable(),
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
                TextColumn::make('accessibility_score')
                    ->label('A11y')
                    ->formatStateUsing(fn ($state) => $state ? round($state) : '-'),
                TextColumn::make('best_practices_score')
                    ->label('BP')
                    ->formatStateUsing(fn ($state) => $state ? round($state) : '-'),
                TextColumn::make('seo_score')
                    ->label('SEO')
                    ->formatStateUsing(fn ($state) => $state ? round($state) : '-'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('page')
                    ->relationship('page', 'name'),
                SelectFilter::make('strategy')
                    ->options([
                        'mobile' => 'Mobile',
                        'desktop' => 'Desktop',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'success' => 'Success',
                        'error' => 'Error',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCrawlResults::route('/'),
            'view' => Pages\ViewCrawlResult::route('/{record}'),
        ];
    }
}
