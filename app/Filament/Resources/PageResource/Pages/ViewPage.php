<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use App\Jobs\AnalyzeBundleSizeJob;
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
                    AnalyzeBundleSizeJob::dispatch($this->record)->onQueue('bundle');
                    
                    Notification::make()
                        ->title('Crawl queued')
                        ->body('PSI and Bundle Size analysis queued.')
                        ->success()
                        ->send();
                }),
            Action::make('analyze_bundle')
                ->label('Analyze Bundle')
                ->icon('heroicon-o-archive-box')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Analyze Bundle Size')
                ->modalDescription('This will analyze the bundle size of this page.')
                ->action(function () {
                    AnalyzeBundleSizeJob::dispatch($this->record)->onQueue('bundle');
                    
                    Notification::make()
                        ->title('Bundle analysis queued')
                        ->body('Bundle size analysis will run shortly.')
                        ->success()
                        ->send();
                }),
            EditAction::make(),
        ];
    }
}
