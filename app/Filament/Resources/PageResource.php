<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Jobs\AnalyzeBundleSizeJob;
use App\Jobs\AnalyzePageJob;
use App\Models\Page;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-globe-alt';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Monitoring';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function form(Schema $form): Schema
    {
        return $form
            ->components([
                Section::make('Page Details')
                    ->schema([
                        Select::make('organization_id')
                            ->relationship('organization', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('url')
                            ->required()
                            ->url()
                            ->maxLength(2048),
                        Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Crawl Settings')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                        TextInput::make('crawl_interval_hours')
                            ->label('Crawl Interval (hours)')
                            ->numeric()
                            ->default(24)
                            ->minValue(1)
                            ->maxValue(168),
                        DateTimePicker::make('next_crawl_at')
                            ->label('Next Crawl')
                            ->nullable(),
                        TextInput::make('filmstrip_retention_count')
                            ->label('Filmstrip Retention')
                            ->helperText('Keep screenshots for last X crawls (leave empty for org default)')
                            ->numeric()
                            ->nullable()
                            ->minValue(1)
                            ->maxValue(100),
                    ])->columns(4),

                Section::make('Status')
                    ->schema([
                        Placeholder::make('last_crawled_at')
                            ->label('Last Crawled')
                            ->content(fn (?Page $record): string => 
                                $record?->last_crawled_at?->diffForHumans() ?? 'Never'
                            ),
                        Placeholder::make('latest_mobile_score')
                            ->label('Latest Mobile Score')
                            ->content(fn (?Page $record): string => 
                                $record?->latestMobileResult?->performance_score 
                                    ? round($record->latestMobileResult->performance_score) 
                                    : 'N/A'
                            ),
                        Placeholder::make('latest_desktop_score')
                            ->label('Latest Desktop Score')
                            ->content(fn (?Page $record): string => 
                                $record?->latestDesktopResult?->performance_score 
                                    ? round($record->latestDesktopResult->performance_score) 
                                    : 'N/A'
                            ),
                    ])
                    ->columns(3)
                    ->visible(fn (?Page $record): bool => $record !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('url')
                    ->searchable()
                    ->limit(40)
                    ->url(fn (Page $record): string => $record->url, true),
                TextColumn::make('organization.name')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('latestMobileResult.performance_score')
                    ->label('Mobile')
                    ->formatStateUsing(fn ($state) => $state ? round($state) : '-')
                    ->color(fn ($state) => match(true) {
                        $state >= 90 => 'success',
                        $state >= 50 => 'warning',
                        $state > 0 => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('latestDesktopResult.performance_score')
                    ->label('Desktop')
                    ->formatStateUsing(fn ($state) => $state ? round($state) : '-')
                    ->color(fn ($state) => match(true) {
                        $state >= 90 => 'success',
                        $state >= 50 => 'warning',
                        $state > 0 => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('crawl_interval_hours')
                    ->label('Interval')
                    ->suffix('h'),
                TextColumn::make('last_crawled_at')
                    ->label('Last Crawl')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('next_crawl_at')
                    ->label('Next Crawl')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('organization')
                    ->relationship('organization', 'name'),
                TernaryFilter::make('is_active'),
            ])
            ->actions([
                Action::make('crawl')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Page $record) {
                        AnalyzePageJob::dispatch($record, 'mobile')->onQueue('psi');
                        AnalyzePageJob::dispatch($record, 'desktop')->onQueue('psi');
                        AnalyzeBundleSizeJob::dispatch($record)->onQueue('bundle');
                        
                        Notification::make()
                            ->title('Crawl queued')
                            ->body('PSI and Bundle Size analysis queued.')
                            ->success()
                            ->send();
                    }),
                Action::make('analyze_bundle')
                    ->label('Bundle')
                    ->icon('heroicon-o-archive-box')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Analyze Bundle Size')
                    ->modalDescription('This will analyze the bundle size of this page.')
                    ->action(function (Page $record) {
                        AnalyzeBundleSizeJob::dispatch($record)->onQueue('bundle');
                        
                        Notification::make()
                            ->title('Bundle analysis queued')
                            ->body('Bundle size analysis will run shortly.')
                            ->success()
                            ->send();
                    }),
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('crawl_selected')
                        ->label('Crawl Selected')
                        ->icon('heroicon-o-arrow-path')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                AnalyzePageJob::dispatch($record, 'mobile')->onQueue('psi');
                                AnalyzePageJob::dispatch($record, 'desktop')->onQueue('psi');
                                AnalyzeBundleSizeJob::dispatch($record)->onQueue('bundle');
                            }
                            
                            Notification::make()
                                ->title('Crawls queued')
                                ->body(count($records) . ' pages queued for PSI + Bundle analysis.')
                                ->success()
                                ->send();
                        }),
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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'view' => Pages\ViewPage::route('/{record}'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
