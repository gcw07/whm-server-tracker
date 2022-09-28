<?php

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\UptimeMonitor\Database\Factories\MonitorFactory;

uses(LazilyRefreshDatabase::class);

it('sends a notification upon uptime check failing', function () {
    Notification::fake();

    User::factory()->create(['email' => 'grant@gwscripts.com']);

    $this->travel(-10)->minutes();

    MonitorFactory::new()->create([
        'url' => 'https://bogus-site-name-1-2-xyz.com',
        'uptime_status' => 'up',
        'uptime_check_enabled' => true,
    ]);

    $this->travelBack();

    $consecutiveFailsNeeded = config('uptime-monitor.uptime_check.fire_monitor_failed_event_after_consecutive_failures');

    foreach (range(1, $consecutiveFailsNeeded) as $index) {
        Artisan::call('monitor:check-uptime');
    }

    Notification::assertCount(1);
});
