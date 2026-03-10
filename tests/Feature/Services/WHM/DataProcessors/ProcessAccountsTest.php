<?php

use App\Models\Account;
use App\Models\Server;
use App\Services\WHM\DataProcessors\ProcessAccounts;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\UptimeMonitor\Models\Monitor;

uses(LazilyRefreshDatabase::class);

function makeApiAccount(array $overrides = []): array
{
    return array_merge([
        'domain' => 'example.com',
        'user' => 'example',
        'ip' => '1.2.3.4',
        'backup' => true,
        'suspended' => false,
        'suspendreason' => 'not suspended',
        'suspendtime' => 0,
        'startdate' => '2025-01-01',
        'diskused' => '100M',
        'disklimit' => '1000M',
        'plan' => '1 Gig',
    ], $overrides);
}

function makeApiData(array $accounts): array
{
    return ['data' => ['acct' => $accounts]];
}

it('suspending an account nulls monitor_id without deleting the account', function () {
    $server = Server::factory()->create();
    $monitor = Monitor::create([
        'url' => 'https://example.com',
        'uptime_check_enabled' => true,
        'certificate_check_enabled' => true,
    ]);

    $account = Account::factory()->create([
        'server_id' => $server->id,
        'domain' => 'example.com',
        'user' => 'example',
        'suspended' => false,
        'monitor_id' => $monitor->id,
    ]);

    $data = makeApiData([makeApiAccount(['suspended' => true, 'suspendreason' => 'overdue'])]);

    (new ProcessAccounts)->execute($server, $data);

    $account->refresh();

    expect($account->exists)->toBeTrue()
        ->and($account->suspended)->toBeTrue()
        ->and($account->monitor_id)->toBeNull();
});

it('creates a monitor for a new unsuspended account', function () {
    $server = Server::factory()->create();

    $data = makeApiData([makeApiAccount()]);

    (new ProcessAccounts)->execute($server, $data);

    $account = $server->fresh()->accounts()->where('user', 'example')->first();
    expect($account)->not->toBeNull()
        ->and($account->monitor_id)->not->toBeNull();

    $this->assertDatabaseHas('monitors', ['url' => 'https://example.com']);
});

it('does not create a monitor for a new suspended account', function () {
    $server = Server::factory()->create();

    $data = makeApiData([makeApiAccount(['suspended' => true])]);

    (new ProcessAccounts)->execute($server, $data);

    $account = $server->fresh()->accounts()->where('user', 'example')->first();
    expect($account)->not->toBeNull()
        ->and($account->monitor_id)->toBeNull();

    $this->assertDatabaseMissing('monitors', ['url' => 'https://example.com']);
});

it('removes accounts that are no longer in the api response', function () {
    $server = Server::factory()->create();
    $staleAccount = Account::factory()->create([
        'server_id' => $server->id,
        'user' => 'stale',
        'domain' => 'stale.com',
        'monitor_id' => null,
    ]);

    $data = makeApiData([makeApiAccount()]);

    (new ProcessAccounts)->execute($server, $data);

    $this->assertDatabaseMissing('accounts', ['id' => $staleAccount->id]);
});

it('returns early when data key is missing', function () {
    $server = Server::factory()->create();

    $result = (new ProcessAccounts)->execute($server, []);

    expect($result)->toBe([]);
});
