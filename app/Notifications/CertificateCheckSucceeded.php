<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Spatie\UptimeMonitor\Events\CertificateCheckSucceeded as ValidCertificateFoundEvent;

class CertificateCheckSucceeded extends BaseNotification
{
    use Queueable;

    public ValidCertificateFoundEvent $event;

    public function __construct(ValidCertificateFoundEvent $event)
    {
        $this->event = $event;
    }

    public function via($notifiable): array
    {
        return [];  // restore mail once selection part is done
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     */
    public function toMail($notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->subject($this->getMessageText())
            ->line($this->getMessageText());

        foreach ($this->getMonitorProperties() as $name => $value) {
            $mailMessage->line($name.': '.$value);
        }

        return $mailMessage;
    }

    public function getMessageText(): string
    {
        return "SSL certificate for {$this->event->monitor->url} is valid";
    }
}
