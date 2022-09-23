<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Spatie\UptimeMonitor\Events\CertificateCheckSucceeded;

class SendCertificateCheckSucceeded
{
    public function __construct()
    {
        //
    }

    public function handle(CertificateCheckSucceeded $event)
    {
        $users = User::all();
        $notification = new \App\Notifications\CertificateCheckSucceeded($event);

        if ($notification->isStillRelevant()) {
            Notification::send($users, $notification);
        }
    }
}
