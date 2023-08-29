<?php

namespace App\Notifications;

use App\Events\DomainNameExpiresSoonEvent as SoonExpiringDomainNameFoundEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class DomainNameExpiresSoon extends BaseNotification
{
    use Queueable;

    public SoonExpiringDomainNameFoundEvent $event;

    public function __construct(SoonExpiringDomainNameFoundEvent $event)
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
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mailMessage = (new MailMessage())
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
        return "Domain name for {$this->getMonitor()->url} expires soon";
    }
}
