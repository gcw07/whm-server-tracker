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

    public function handle(CertificateExpiresSoon $event): void
    {
        $users = User::forNotificationType('certificate_expires_soon')->get();
        $notification = new \App\Notifications\CertificateExpiresSoon($event);

        if ($notification->isStillRelevant()) {
            Notification::send($users, $notification);
        }
    }
}
