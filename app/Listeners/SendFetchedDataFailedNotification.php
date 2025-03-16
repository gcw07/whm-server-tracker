<?php

namespace App\Listeners;

use App\Events\FetchedDataFailedEvent;
use App\Notifications\FetchedDataFailed;
use Illuminate\Support\Facades\Notification;

class SendFetchedDataFailedNotification
{
    public function __construct()
    {
        //
    }

    public function handle(FetchedDataFailedEvent $event): void
    {
        Notification::route('mail', config('server-tracker.notifications.mail.to'))
            ->route('slack', config('server-tracker.notifications.slack.webhook_url'))
            ->notify(new FetchedDataFailed($event));
    }
}
