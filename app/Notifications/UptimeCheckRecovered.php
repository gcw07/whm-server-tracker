<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Spatie\UptimeMonitor\Events\UptimeCheckRecovered as MonitorRecoveredEvent;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;

class UptimeCheckRecovered extends BaseNotification
{
    use Queueable;

    public MonitorRecoveredEvent $event;

    public function __construct(MonitorRecoveredEvent $event)
    {
        $this->event = $event;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->success()
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
        $extraProperties = [
            "Downtime: {$this->event->downtimePeriod->duration()}" => $this->event->downtimePeriod->toText(),
        ];

        return parent::getMonitorProperties($extraProperties);
    }

    public function isStillRelevant(): bool
    {
        return $this->getMonitor()->uptime_status == UptimeStatus::UP;
    }

    public function getMessageText(): string
    {
        return "{$this->getMonitor()->url} has recovered after {$this->event->downtimePeriod->duration()}";
    }
}
