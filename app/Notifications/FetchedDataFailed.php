<?php

namespace App\Notifications;

use App\Events\FetchedDataFailedEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class FetchedDataFailed extends Notification
{
    use Queueable;

    public FetchedDataFailedEvent $event;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(FetchedDataFailedEvent $event)
    {
        $this->event = $event;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return config('server-tracker.notifications.notifications.fetched_data_failed');
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
            ->error()
            ->subject('Server Tracker')
            ->greeting('Hello,')
            ->line('The following server has failed to update:')
            ->line(new HtmlString('<b>'.$this->event->server->name.'</b>'))
            ->line('With the error messages provided:');

        foreach ($this->event->messages as $message) {
            $mailMessage->line(new HtmlString('<b>'.$message['type'].':</b> ' . $message['message']));
        }

        $mailMessage->line('Thank you for using our application!');

        return $mailMessage;
    }

    public function toSlack($notifiable)
    {
        // Add Slack Message Later
    }
}
