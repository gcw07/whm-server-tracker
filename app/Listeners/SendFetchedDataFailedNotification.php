<?php

namespace App\Listeners;

use App\Events\FetchedDataFailedEvent;
use App\Notifications\FetchedDataFailed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendFetchedDataFailedNotification
{
    public function __construct()
    {
        //
    }

    public function handle(FetchedDataFailedEvent $event)
    {
        Notification::route('mail', config('server-tracker.notifications.mail.to'))
            ->route('slack', config('server-tracker.notifications.slack.webhook_url'))
            ->notify(new FetchedDataFailed($event));
    }
}
