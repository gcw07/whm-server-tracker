<?php

namespace App\Listeners;

use App\Events\FetchedDataSucceededEvent;
use App\Notifications\FetchedDataSucceeded;
use Illuminate\Support\Facades\Notification;

class SendFetchedDataSucceededNotification
{
    public function __construct()
    {
        //
    }

    public function handle(FetchedDataSucceededEvent $event): void
    {
        Notification::route('mail', config('server-tracker.notifications.mail.to'))
            ->route('slack', config('server-tracker.notifications.slack.webhook_url'))
            ->notify(new FetchedDataSucceeded($event));
    }
}
