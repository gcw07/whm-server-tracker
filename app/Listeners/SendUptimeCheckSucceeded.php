<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Spatie\UptimeMonitor\Events\UptimeCheckSucceeded;

class SendUptimeCheckSucceeded
{
    public function __construct()
    {
        //
    }

    public function handle(UptimeCheckSucceeded $event)
    {
        $users = User::all();
        $notification = new \App\Notifications\UptimeCheckSucceeded($event);

        if ($notification->isStillRelevant()) {
            Notification::send($users, $notification);
        }
    }
}
