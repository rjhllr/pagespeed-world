<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use App\Jobs\AnalyzePageJob;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewPage extends ViewRecord
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('crawl')
                ->label('Crawl Now')
                ->icon('heroicon-o-arrow-path')
                ->color('success')
                ->requiresConfirmation()
                ->action(function () {
                    AnalyzePageJob::dispatch($this->record, 'mobile')->onQueue('psi');
                    AnalyzePageJob::dispatch($this->record, 'desktop')->onQueue('psi');
                    
                    Notification::make()
                        ->title('Crawl queued')
                        ->body('The page will be analyzed shortly.')
                        ->success()
                        ->send();
                }),
            EditAction::make(),
        ];
    }
}
