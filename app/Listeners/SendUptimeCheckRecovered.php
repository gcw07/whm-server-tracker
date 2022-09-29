<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Spatie\UptimeMonitor\Events\UptimeCheckRecovered;

class SendUptimeCheckRecovered
{
    public function __construct()
    {
        //
    }

    public function handle(UptimeCheckRecovered $event)
    {
        $users = User::forNotificationType('uptime_check_recovered')->get();
        $notification = new \App\Notifications\UptimeCheckRecovered($event);

        if ($notification->isStillRelevant()) {
            Notification::send($users, $notification);
        }
    }
}
