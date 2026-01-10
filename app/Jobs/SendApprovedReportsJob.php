<?php

namespace App\Jobs;

use App\Models\Report;
use App\Notifications\ReportNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendApprovedReportsJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $reports = Report::where('status', 'approved')
            ->whereNull('sent_at')
            ->get();

        foreach ($reports as $report) {
            $this->sendReport($report);
        }

        Log::info('Approved reports sent', ['count' => $reports->count()]);
    }

    private function sendReport(Report $report): void
    {
        try {
            $recipients = $report->recipients ?? [];
            
            if (empty($recipients)) {
                $recipients = $report->organization->users->pluck('email')->toArray();
            }

            foreach ($recipients as $email) {
                Notification::route('mail', $email)
                    ->notify(new ReportNotification($report));
            }

            $report->markAsSent();

            Log::info('Report sent', [
                'report_id' => $report->id,
                'recipients' => count($recipients),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send report', [
                'report_id' => $report->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
