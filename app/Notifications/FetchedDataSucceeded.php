<?php

namespace App\Notifications;

use App\Events\FetchedDataSucceededEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class FetchedDataSucceeded extends Notification
{
    use Queueable;

    public FetchedDataSucceededEvent $event;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(FetchedDataSucceededEvent $event)
    {
        $this->event = $event;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     */
    public function via($notifiable): array
    {
        return config('server-tracker.notifications.notifications.fetched_data_succeeded');
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
            ->subject('Server Tracker')
            ->greeting('Hello,')
            ->line('The following server has successfully updated:')
            ->line(new HtmlString('<b>'.$this->event->server->name.'</b>'))
            ->line('Thank you for using our application!');
    }

    public function toSlack($notifiable)
    {
        // Add Slack Message Later
    }
}
