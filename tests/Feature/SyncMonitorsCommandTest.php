<?php

use App\Models\Account;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\UptimeMonitor\Models\Monitor;

uses(LazilyRefreshDatabase::class);

it('creates a monitor for a non-suspended account', function () {
    Account::factory()->create([
        'domain' => 'mysite.com',
        'suspended' => false,
        'monitor_id' => null,
    ]);

    $this->artisan('server-tracker:sync-monitors')->assertSuccessful();

    $this->assertDatabaseHas('monitors', ['url' => 'https://mysite.com']);
});

it('skips suspended accounts', function () {
    Account::factory()->create([
        'domain' => 'suspendedsite.com',
        'suspended' => true,
        'monitor_id' => null,
    ]);

    $this->artisan('server-tracker:sync-monitors')->assertSuccessful();

    $this->assertDatabaseMissing('monitors', ['url' => 'https://suspendedsite.com']);
});

it('updates the monitor_id on an account that has no monitor_id set', function () {
    $existingMonitor = Monitor::create([
        'url' => 'https://mysite.com',
        'uptime_check_enabled' => true,
        'certificate_check_enabled' => true,
    ]);

    $account = Account::factory()->create([
        'domain' => 'mysite.com',
        'suspended' => false,
        'monitor_id' => null,
    ]);

    $this->artisan('server-tracker:sync-monitors')->assertSuccessful();

    expect($account->fresh()->monitor_id)->toBe($existingMonitor->id);
    expect(Monitor::count())->toBe(1);
});

it('does not update monitor_id when account already has the correct monitor_id', function () {
    $monitor = Monitor::create([
        'url' => 'https://mysite.com',
        'uptime_check_enabled' => true,
        'certificate_check_enabled' => true,
    ]);

    $account = Account::factory()->create([
        'domain' => 'mysite.com',
        'suspended' => false,
        'monitor_id' => $monitor->id,
    ]);

    $this->artisan('server-tracker:sync-monitors')->assertSuccessful();

    expect($account->fresh()->monitor_id)->toBe($monitor->id);
    expect(Monitor::count())->toBe(1);
});

it('deletes orphaned monitors not linked to any account', function () {
    Monitor::create([
        'url' => 'https://orphan.com',
        'uptime_check_enabled' => true,
        'certificate_check_enabled' => true,
    ]);

    $this->artisan('server-tracker:sync-monitors')->assertSuccessful();

    $this->assertDatabaseMissing('monitors', ['url' => 'https://orphan.com']);
});

it('does not delete monitors that are still linked to an account', function () {
    $monitor = Monitor::create([
        'url' => 'https://mysite.com',
        'uptime_check_enabled' => true,
        'certificate_check_enabled' => true,
    ]);

    Account::factory()->create([
        'domain' => 'mysite.com',
        'suspended' => false,
        'monitor_id' => $monitor->id,
    ]);

    $this->artisan('server-tracker:sync-monitors')->assertSuccessful();

    $this->assertDatabaseHas('monitors', ['url' => 'https://mysite.com']);
});
