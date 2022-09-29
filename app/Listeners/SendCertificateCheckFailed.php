<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Spatie\UptimeMonitor\Events\CertificateCheckFailed;

class SendCertificateCheckFailed
{
    public function __construct()
    {
        //
    }

    public function handle(CertificateCheckFailed $event)
    {
        $users = User::forNotificationType('certificate_check_failed')->get();
        $notification = new \App\Notifications\CertificateCheckFailed($event);

        if ($notification->isStillRelevant()) {
            Notification::send($users, $notification);
        }
    }
}
