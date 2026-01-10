<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BundleSizeResource\Pages;
use App\Models\BundleSize;
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

class BundleSizeResource extends Resource
{
    protected static ?string $model = BundleSize::class;
    protected static ?string $navigationLabel = 'Bundle Sizes';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-archive-box';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Monitoring';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
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
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'success' => 'Success',
                                'error' => 'Error',
                            ])
                            ->disabled(),
                        TextInput::make('created_at')
                            ->label('Crawled At')
                            ->disabled(),
                    ])->columns(3),

                Section::make('Total Sizes')
                    ->schema([
                        TextInput::make('total_size')
                            ->label('Total (Uncompressed)')
                            ->formatStateUsing(fn ($state) => $state ? BundleSize::formatBytes($state) : '-')
                            ->disabled(),
                        TextInput::make('total_transfer_size')
                            ->label('Total (Transfer/Gzipped)')
                            ->formatStateUsing(fn ($state) => $state ? BundleSize::formatBytes($state) : '-')
                            ->disabled(),
                        TextInput::make('compression_ratio')
                            ->label('Compression Ratio')
                            ->formatStateUsing(fn ($state) => $state !== null ? $state . '%' : '-')
                            ->disabled(),
                        TextInput::make('total_requests')
                            ->label('Total Requests')
                            ->disabled(),
                    ])->columns(4),

                Section::make('Size Breakdown')
                    ->schema([
                        TextInput::make('javascript_size')
                            ->label('JavaScript')
                            ->formatStateUsing(fn ($state) => $state ? BundleSize::formatBytes($state) : '-')
                            ->disabled(),
                        TextInput::make('css_size')
                            ->label('CSS')
                            ->formatStateUsing(fn ($state) => $state ? BundleSize::formatBytes($state) : '-')
                            ->disabled(),
                        TextInput::make('image_size')
                            ->label('Images')
                            ->formatStateUsing(fn ($state) => $state ? BundleSize::formatBytes($state) : '-')
                            ->disabled(),
                        TextInput::make('font_size')
                            ->label('Fonts')
                            ->formatStateUsing(fn ($state) => $state ? BundleSize::formatBytes($state) : '-')
                            ->disabled(),
                        TextInput::make('html_size')
                            ->label('HTML')
                            ->formatStateUsing(fn ($state) => $state ? BundleSize::formatBytes($state) : '-')
                            ->disabled(),
                        TextInput::make('other_size')
                            ->label('Other')
                            ->formatStateUsing(fn ($state) => $state ? BundleSize::formatBytes($state) : '-')
                            ->disabled(),
                    ])->columns(3),

                Section::make('Download Times')
                    ->schema([
                        TextInput::make('load_time')
                            ->label('Page Load Time')
                            ->formatStateUsing(fn ($state) => $state ? number_format($state / 1000, 2) . 's' : '-')
                            ->disabled(),
                        TextInput::make('dom_content_loaded')
                            ->label('DOM Content Loaded')
                            ->formatStateUsing(fn ($state) => $state ? number_format($state / 1000, 2) . 's' : '-')
                            ->disabled(),
                        TextInput::make('slow_request_count')
                            ->label('Slow Requests (>1s)')
                            ->disabled(),
                        TextInput::make('javascript_download_time')
                            ->label('JS Download')
                            ->formatStateUsing(fn ($state) => $state ? $state . 'ms' : '-')
                            ->disabled(),
                        TextInput::make('css_download_time')
                            ->label('CSS Download')
                            ->formatStateUsing(fn ($state) => $state ? $state . 'ms' : '-')
                            ->disabled(),
                        TextInput::make('image_download_time')
                            ->label('Image Download')
                            ->formatStateUsing(fn ($state) => $state ? $state . 'ms' : '-')
                            ->disabled(),
                    ])->columns(3),

                Section::make('Error Details')
                    ->schema([
                        Textarea::make('error_message')
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (?BundleSize $record): bool => $record?->status === 'error'),
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
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'success' => 'success',
                        'error' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('total_size')
                    ->label('Total Size')
                    ->formatStateUsing(fn ($state) => $state ? BundleSize::formatBytes($state) : '-')
                    ->sortable(),
                TextColumn::make('total_transfer_size')
                    ->label('Transfer')
                    ->formatStateUsing(fn ($state) => $state ? BundleSize::formatBytes($state) : '-'),
                TextColumn::make('javascript_size')
                    ->label('JS')
                    ->formatStateUsing(fn ($state) => $state ? BundleSize::formatBytes($state) : '-'),
                TextColumn::make('total_requests')
                    ->label('Requests'),
                TextColumn::make('load_time')
                    ->label('Load Time')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state / 1000, 2) . 's' : '-')
                    ->sortable(),
                TextColumn::make('slow_request_count')
                    ->label('Slow')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'gray'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('page')
                    ->relationship('page', 'name'),
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
            'index' => Pages\ListBundleSizes::route('/'),
            'view' => Pages\ViewBundleSize::route('/{record}'),
        ];
    }
}
