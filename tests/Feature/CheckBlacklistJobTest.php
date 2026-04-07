<?php

use App\Enums\BlacklistStatusEnum;
use App\Jobs\CheckBlacklistJob;
use App\Models\Account;
use App\Models\Monitor;
use App\Models\MonitorBlacklistCheck;
use App\Models\MonitorBlacklistResult;
use App\Models\Server;
use App\Observers\MonitorObserver;
use App\Services\Blacklist\BlacklistChecker;
use App\Services\Blacklist\BlacklistResult;
use App\Services\Blacklist\Contracts\BlacklistDriver;
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

test('blacklist checker uses non-suspended account server ip when multiple accounts exist', function () {
    MonitorFactory::new()->create(['url' => 'https://myserver.com']);
    $monitor = Monitor::first();
    MonitorBlacklistCheck::create(['monitor_id' => $monitor->id]);

    // Suspended account (old server) — would be returned first by naive ->first()
    $oldServer = Server::factory()->create(['address' => '1.1.1.1']);
    Account::factory()->create(['monitor_id' => $monitor->id, 'server_id' => $oldServer->id, 'suspended' => true]);

    // Non-suspended account (new server)
    $newServer = Server::factory()->create(['address' => '2.2.2.2']);
    Account::factory()->create(['monitor_id' => $monitor->id, 'server_id' => $newServer->id, 'suspended' => false]);

    $checker = new class extends BlacklistChecker
    {
        protected function ipBasedDrivers(): array
        {
            return [new class implements BlacklistDriver
            {
                public function name(): string
                {
                    return 'Test';
                }

                public function url(): string
                {
                    return 'https://example.com';
                }

                public function check(string $domain, ?string $ip): BlacklistResult
                {
                    return BlacklistResult::clean($this->name());
                }
            }];
        }

        protected function domainBasedDrivers(): array
        {
            return [];
        }
    };

    $checker->check($monitor);

    expect(
        MonitorBlacklistResult::where('monitor_id', $monitor->id)->where('driver', 'Test')->value('checked_value')
    )->toBe('2.2.2.2');
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
