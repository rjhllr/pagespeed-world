<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Report $report
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject($this->report->subject)
            ->greeting("Hello!");

        if ($this->report->type === 'anomaly') {
            $message->line('An anomaly has been detected in your website performance.')
                ->line($this->report->content);
        } else {
            $message->line('Here is your weekly performance report.')
                ->line($this->report->content);
        }

        if ($this->report->page) {
            $message->action('View Page Details', url('/dashboard/pages/' . $this->report->page_id));
        } else {
            $message->action('View Dashboard', url('/dashboard'));
        }

        return $message->line('Thank you for using PSI Monitor!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'report_id' => $this->report->id,
            'type' => $this->report->type,
            'subject' => $this->report->subject,
        ];
    }
}
