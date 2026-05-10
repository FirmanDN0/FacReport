<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;

class ReportNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    protected $report;
    protected $message;
    protected $type;

    public function __construct($report, $message, $type = 'status_update')
    {
        $this->report = $report;
        $this->message = $message;
        $this->type = $type;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => $this->message,
            'url' => $notifiable->role === 'admin' 
                ? route('admin.reports.show', $this->report->id)
                : route('reports.show', $this->report->id),
            'type' => $this->type,
            'facility_name' => $this->report->facility_name,
        ]);
    }

    public function toArray($notifiable)
    {
        // Determine URL based on role
        $url = $notifiable->role === 'admin' 
            ? route('admin.reports.show', $this->report->id)
            : route('reports.show', $this->report->id);

        return [
            'report_id' => $this->report->id,
            'report_code' => $this->report->report_code,
            'facility_name' => $this->report->facility_name,
            'message' => $this->message,
            'type' => $this->type,
            'url' => $url,
        ];
    }
}
