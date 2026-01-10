<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Filament\Resources\ReportResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewReport extends ViewRecord
{
    protected static string $resource = ReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn (): bool => $this->record->isPending())
                ->form([
                    Textarea::make('notes')
                        ->label('Review Notes (optional)'),
                ])
                ->action(function (array $data) {
                    $this->record->approve(Auth::user(), $data['notes'] ?? null);
                    
                    Notification::make()
                        ->title('Report approved')
                        ->body('The report has been approved and will be sent.')
                        ->success()
                        ->send();
                        
                    $this->redirect(ReportResource::getUrl('index'));
                }),
            Action::make('reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn (): bool => $this->record->isPending())
                ->form([
                    Textarea::make('notes')
                        ->label('Rejection Reason')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->record->reject(Auth::user(), $data['notes']);
                    
                    Notification::make()
                        ->title('Report rejected')
                        ->body('The report has been rejected.')
                        ->warning()
                        ->send();
                        
                    $this->redirect(ReportResource::getUrl('index'));
                }),
            DeleteAction::make(),
        ];
    }
}
