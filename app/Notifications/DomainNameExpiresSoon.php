<?php

namespace App\Notifications;

use App\Events\DomainNameExpiresSoonEvent as SoonExpiringDomainNameFoundEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DomainNameExpiresSoon extends Notification
{
    use Queueable;

    public SoonExpiringDomainNameFoundEvent $event;

    public function __construct(SoonExpiringDomainNameFoundEvent $event)
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
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->success()
            ->subject($this->getSubjectText())
            ->greeting('Domain Name Expiring')
            ->line("The domain name for {$this->event->monitor->url} expires soon:")
            ->line("Domain name expires in {$this->event->monitor->domain_name_expiration_date->diffForHumans()} on:")
            ->line("{$this->event->monitor->domain_name_expiration_date->toDayDateTimeString()}");
    }

    protected function getSubjectText(): string
    {
        return "Domain name for {$this->event->monitor->url} expires soon";
    }

    public function isStillRelevant(): bool
    {
        return true;
    }
}
