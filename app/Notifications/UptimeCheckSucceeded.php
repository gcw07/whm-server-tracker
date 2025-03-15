<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Spatie\UptimeMonitor\Events\UptimeCheckSucceeded as MonitorSucceededEvent;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;

class UptimeCheckSucceeded extends BaseNotification
{
    use Queueable;

    public MonitorSucceededEvent $event;

    public function __construct(MonitorSucceededEvent $event)
    {
        $this->event = $event;
    }

    public function via($notifiable)
    {
        return []; // restore mail once selection part is done
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mailMessage = (new MailMessage)
            ->subject($this->getMessageText())
            ->line($this->getMessageText())
            ->line($this->getLocationDescription());

        foreach ($this->getMonitorProperties() as $name => $value) {
            $mailMessage->line($name.': '.$value);
        }

        return $mailMessage;
    }

    public function isStillRelevant(): bool
    {
        return $this->getMonitor()->uptime_status != UptimeStatus::DOWN;
    }

    public function getMessageText(): string
    {
        return "{$this->getMonitor()->url} is up";
    }
}
