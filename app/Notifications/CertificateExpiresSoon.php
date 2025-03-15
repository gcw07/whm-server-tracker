<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Spatie\UptimeMonitor\Events\CertificateExpiresSoon as SoonExpiringSslCertificateFoundEvent;

class CertificateExpiresSoon extends BaseNotification
{
    use Queueable;

    public SoonExpiringSslCertificateFoundEvent $event;

    public function __construct(SoonExpiringSslCertificateFoundEvent $event)
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
            ->error()
            ->subject($this->getMessageText())
            ->line($this->getMessageText());

        foreach ($this->getMonitorProperties() as $name => $value) {
            $mailMessage->line($name.': '.$value);
        }

        return $mailMessage;
    }

    protected function getMessageText(): string
    {
        return "SSL certificate for {$this->getMonitor()->url} expires soon";
    }
}
