<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Models\Report;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
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
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-document-text';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Reports';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $form): Schema
    {
        return $form
            ->components([
                Section::make('Report Details')
                    ->schema([
                        Select::make('organization_id')
                            ->relationship('organization', 'name')
                            ->disabled(),
                        Select::make('page_id')
                            ->relationship('page', 'name')
                            ->disabled()
                            ->placeholder('All pages'),
                        Select::make('type')
                            ->options([
                                'weekly' => 'Weekly',
                                'anomaly' => 'Anomaly',
                                'custom' => 'Custom',
                            ])
                            ->disabled(),
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending Review',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                                'sent' => 'Sent',
                            ])
                            ->disabled(),
                    ])->columns(2),

                Section::make('Content')
                    ->schema([
                        TextInput::make('subject')
                            ->disabled()
                            ->columnSpanFull(),
                        MarkdownEditor::make('content')
                            ->disabled()
                            ->columnSpanFull(),
                    ]),

                Section::make('Review')
                    ->schema([
                        Textarea::make('review_notes')
                            ->label('Review Notes')
                            ->helperText('Add any notes before approving or rejecting'),
                        Placeholder::make('reviewer_info')
                            ->label('Reviewed By')
                            ->content(fn (?Report $record): string => 
                                $record?->reviewer 
                                    ? "{$record->reviewer->name} at {$record->reviewed_at->format('Y-m-d H:i')}"
                                    : 'Not yet reviewed'
                            ),
                    ])
                    ->columns(2),

                Section::make('Recipients')
                    ->schema([
                        TagsInput::make('recipients')
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subject')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('organization.name')
                    ->sortable(),
                TextColumn::make('page.name')
                    ->placeholder('All pages')
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'weekly' => 'primary',
                        'anomaly' => 'danger',
                        'custom' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'info',
                        'rejected' => 'danger',
                        'sent' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('reviewer.name')
                    ->label('Reviewed By')
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('sent_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('—'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('organization')
                    ->relationship('organization', 'name'),
                SelectFilter::make('type')
                    ->options([
                        'weekly' => 'Weekly',
                        'anomaly' => 'Anomaly',
                        'custom' => 'Custom',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'sent' => 'Sent',
                    ])
                    ->default('pending'),
            ])
            ->actions([
                Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Report $record): bool => $record->isPending())
                    ->form([
                        Textarea::make('notes')
                            ->label('Review Notes (optional)'),
                    ])
                    ->action(function (Report $record, array $data) {
                        $record->approve(Auth::user(), $data['notes'] ?? null);
                        
                        Notification::make()
                            ->title('Report approved')
                            ->body('The report has been approved and will be sent.')
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Report $record): bool => $record->isPending())
                    ->form([
                        Textarea::make('notes')
                            ->label('Rejection Reason')
                            ->required(),
                    ])
                    ->action(function (Report $record, array $data) {
                        $record->reject(Auth::user(), $data['notes']);
                        
                        Notification::make()
                            ->title('Report rejected')
                            ->body('The report has been rejected.')
                            ->warning()
                            ->send();
                    }),
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('approve_selected')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                if ($record->isPending()) {
                                    $record->approve(Auth::user());
                                }
                            }
                            
                            Notification::make()
                                ->title('Reports approved')
                                ->success()
                                ->send();
                        }),
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
            'index' => Pages\ListReports::route('/'),
            'view' => Pages\ViewReport::route('/{record}'),
        ];
    }
}
