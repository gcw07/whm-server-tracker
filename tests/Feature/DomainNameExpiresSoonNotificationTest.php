<?php

use App\Events\DomainNameExpiresSoonEvent;
use App\Models\Monitor;
use App\Notifications\DomainNameExpiresSoon;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('renders the mail notification without errors', function () {
    $monitor = Monitor::create([
        'url' => 'https://example.com',
        'uptime_check_enabled' => true,
        'certificate_check_enabled' => false,
    ]);

    $date = Carbon::now()->addDays(10);
    $event = new DomainNameExpiresSoonEvent($monitor, $date);
    $notification = new DomainNameExpiresSoon($event);

    $mail = $notification->toMail(null);

    expect($mail->introLines)
        ->toContain("Domain name expires in {$date->diffForHumans()} on:");
});
