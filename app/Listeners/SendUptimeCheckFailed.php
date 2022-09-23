<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Spatie\UptimeMonitor\Events\UptimeCheckFailed;

class SendUptimeCheckFailed
{

    public function __construct()
    {
        //
    }

    public function handle(UptimeCheckFailed $event)
    {
        $users = User::all();
        $notification = new \App\Notifications\UptimeCheckFailed($event);

        if ($notification->isStillRelevant()) {
            Notification::send($users, $notification);
        }
    }
}
