<?php

namespace App\Listeners;

use App\Events\DomainNameExpiresSoonEvent;
use App\Models\User;
use App\Notifications\DomainNameExpiresSoon;
use Illuminate\Support\Facades\Notification;

class SendDomainNameExpiresSoon
{
    public function __construct()
    {
        //
    }

    public function handle(DomainNameExpiresSoonEvent $event): void
    {
        $users = User::forNotificationType('domain_name_expires_soon')->get();
        $notification = new DomainNameExpiresSoon($event);

        if ($notification->isStillRelevant()) {
            Notification::send($users, $notification);
        }
    }
}
