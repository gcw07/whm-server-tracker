<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Spatie\UptimeMonitor\Events\UptimeCheckFailed as MonitorFailedEvent;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;

class UptimeCheckFailed extends BaseNotification
{
    use Queueable;

    public MonitorFailedEvent $event;

    public function __construct(MonitorFailedEvent $event)
    {
        $this->event = $event;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mailMessage = (new MailMessage())
            ->error()
            ->subject($this->getMessageText())
            ->line($this->getMessageText())
            ->line($this->getLocationDescription());

        foreach ($this->getMonitorProperties() as $name => $value) {
            $mailMessage->line($name.': '.$value);
        }

        return $mailMessage;
    }

    public function getMonitorProperties($extraProperties = []): array
    {
        $since = "Since {$this->event->downtimePeriod->startDateTime->format('H:i')}";
        $date = $this->event->monitor->formattedLastUpdatedStatusChangeDate();

        $extraProperties = [
            $since => $date,
            'Failure reason' => $this->getMonitor()->uptime_check_failure_reason,
        ];

        return parent::getMonitorProperties($extraProperties);
    }

    public function isStillRelevant(): bool
    {
        return $this->getMonitor()->uptime_status == UptimeStatus::DOWN;
    }

    protected function getMessageText(): string
    {
        return "{$this->getMonitor()->url} seems down";
    }
}
