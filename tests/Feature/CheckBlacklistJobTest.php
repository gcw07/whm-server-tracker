<?php

use App\Enums\BlacklistStatusEnum;
use App\Jobs\CheckBlacklistJob;
use App\Models\Monitor;
use App\Models\MonitorBlacklistCheck;
use App\Models\MonitorBlacklistResult;
use App\Observers\MonitorObserver;
use App\Services\Blacklist\BlacklistChecker;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\UptimeMonitor\Database\Factories\MonitorFactory;

uses(LazilyRefreshDatabase::class);

test('check blacklist job runs against a monitor', function () {
    MonitorFactory::new()->create(['url' => 'https://myserver.com']);
    $monitor = Monitor::first();

    $job = new CheckBlacklistJob($monitor);

    expect($job->monitor->id)->toBe($monitor->id);
});

test('check blacklist job delegates to BlacklistChecker', function () {
    MonitorFactory::new()->create(['url' => 'https://myserver.com']);
    $monitor = Monitor::first();

    $checker = Mockery::mock(BlacklistChecker::class);
    $checker->shouldReceive('check')->once()->with(Mockery::on(fn ($m) => $m->id === $monitor->id));
    app()->instance(BlacklistChecker::class, $checker);

    CheckBlacklistJob::dispatchSync($monitor);
});

test('check blacklist job sets valid status when no lists match', function () {
    MonitorFactory::new()->create(['url' => 'https://myserver.com']);
    $monitor = Monitor::first();
    MonitorBlacklistCheck::create(['monitor_id' => $monitor->id]);

    $checker = Mockery::mock(BlacklistChecker::class);
    $checker->shouldReceive('check')->once()->andReturnUsing(function (Monitor $m) {
        MonitorBlacklistCheck::where('monitor_id', $m->id)
            ->update(['status' => BlacklistStatusEnum::Valid->value]);
    });
    app()->instance(BlacklistChecker::class, $checker);

    CheckBlacklistJob::dispatchSync($monitor);

    expect($monitor->blacklistCheck->fresh()->status)->toBe(BlacklistStatusEnum::Valid);
});

test('check blacklist job sets invalid status and stores per-driver result when a list matches', function () {
    MonitorFactory::new()->create(['url' => 'https://myserver.com']);
    $monitor = Monitor::first();
    MonitorBlacklistCheck::create(['monitor_id' => $monitor->id]);

    $checker = Mockery::mock(BlacklistChecker::class);
    $checker->shouldReceive('check')->once()->andReturnUsing(function (Monitor $m) {
        MonitorBlacklistCheck::where('monitor_id', $m->id)
            ->update(['status' => BlacklistStatusEnum::Invalid->value]);
        MonitorBlacklistResult::create([
            'monitor_id' => $m->id,
            'driver' => 'SpamCop',
            'listed' => true,
            'failure_reason' => 'Listed on bl.spamcop.net',
            'checked_at' => now(),
        ]);
    });
    app()->instance(BlacklistChecker::class, $checker);

    CheckBlacklistJob::dispatchSync($monitor);

    expect($monitor->blacklistCheck->fresh()->status)->toBe(BlacklistStatusEnum::Invalid);

    $result = MonitorBlacklistResult::where('monitor_id', $monitor->id)->where('driver', 'SpamCop')->first();
    expect($result->listed)->toBeTrue();
    expect($result->failure_reason)->toBe('Listed on bl.spamcop.net');
    expect($result->checked_at)->not->toBeNull();
});

test('observer pre-creates result rows for all drivers when monitor is created', function () {
    // MonitorFactory uses the Spatie base model which does not fire App\Models\Monitor
    // observers — invoke the observer directly to verify its behaviour.
    MonitorFactory::new()->create(['url' => 'https://myserver.com']);
    $monitor = Monitor::first();

    (new MonitorObserver)->created($monitor);

    $driverNames = BlacklistChecker::driverNames();

    expect(MonitorBlacklistResult::where('monitor_id', $monitor->id)->count())->toBe(count($driverNames));

    foreach ($driverNames as $name) {
        expect(
            MonitorBlacklistResult::where('monitor_id', $monitor->id)->where('driver', $name)->exists()
        )->toBeTrue();
    }
});
