<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Spatie\UptimeMonitor\Events\CertificateExpiresSoon;

class SendCertificateExpiresSoon
{

    public function __construct()
    {
        //
    }

    public function handle(CertificateExpiresSoon $event)
    {
        $users = User::all();
        $notification = new \App\Notifications\CertificateExpiresSoon($event);

        if ($notification->isStillRelevant()) {
            Notification::send($users, $notification);
        }
    }
}
