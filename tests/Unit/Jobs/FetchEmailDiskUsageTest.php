<?php

use App\Jobs\FetchEmailDiskUsageJob;
use App\Models\Account;
use App\Models\AccountEmail;
use App\Models\Server;
use App\Services\WHM\WhmApi;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\Factories\WhmApiFake;

uses(LazilyRefreshDatabase::class);

it('stores email disk usage records for each account', function () {
    $server = Server::factory()->create(['token' => 'valid-token']);

    Account::factory()->create([
        'server_id' => $server->id,
        'user' => 'mysite',
    ]);

    $fake = new WhmApiFake;
    $this->app->instance(WhmApi::class, $fake);

    dispatch(new FetchEmailDiskUsageJob($server));

    $emails = AccountEmail::all();
    expect($emails)->toHaveCount(2);
    expect($emails->pluck('email')->toArray())->toContain('info@mysite.com', 'admin@mysite.com');
});

it('correctly maps email disk usage fields', function () {
    $server = Server::factory()->create(['token' => 'valid-token']);
    Account::factory()->create(['server_id' => $server->id, 'user' => 'mysite']);

    $fake = new WhmApiFake;
    $this->app->instance(WhmApi::class, $fake);

    dispatch(new FetchEmailDiskUsageJob($server));

    $email = AccountEmail::where('email', 'info@mysite.com')->first();
    expect($email->user)->toBe('info');
    expect($email->domain)->toBe('mysite.com');
    expect($email->disk_used)->toBe(1024000);
    expect($email->disk_quota)->toBe(524288000);
    expect($email->disk_used_percent)->toBe(0.19);
    expect($email->suspended_incoming)->toBeFalse();
    expect($email->suspended_login)->toBeFalse();

    $admin = AccountEmail::where('email', 'admin@mysite.com')->first();
    expect($admin->disk_quota)->toBeNull();
});

it('removes stale email records when an email no longer exists', function () {
    $server = Server::factory()->create(['token' => 'valid-token']);
    $account = Account::factory()->create(['server_id' => $server->id, 'user' => 'mysite']);

    AccountEmail::factory()->create([
        'account_id' => $account->id,
        'email' => 'stale@mysite.com',
        'user' => 'stale',
        'domain' => 'mysite.com',
    ]);

    $fake = new WhmApiFake;
    $this->app->instance(WhmApi::class, $fake);

    dispatch(new FetchEmailDiskUsageJob($server));

    expect(AccountEmail::where('email', 'stale@mysite.com')->exists())->toBeFalse();
    expect(AccountEmail::where('email', 'info@mysite.com')->exists())->toBeTrue();
});

it('handles accounts with no email accounts gracefully', function () {
    $server = Server::factory()->create(['token' => 'valid-token']);
    Account::factory()->create(['server_id' => $server->id, 'user' => 'mysite']);

    $fake = new class extends WhmApiFake
    {
        protected function getEmailDiskUsageData(string $username): array
        {
            return ['result' => ['data' => []]];
        }
    };

    $this->app->instance(WhmApi::class, $fake);

    dispatch(new FetchEmailDiskUsageJob($server));

    expect(AccountEmail::count())->toBe(0);
});

it('stores email records for multiple accounts', function () {
    $server = Server::factory()->create(['token' => 'valid-token']);
    Account::factory()->create(['server_id' => $server->id, 'user' => 'mysite']);
    Account::factory()->create(['server_id' => $server->id, 'user' => 'super']);

    $fake = new WhmApiFake;
    $this->app->instance(WhmApi::class, $fake);

    dispatch(new FetchEmailDiskUsageJob($server));

    expect(AccountEmail::count())->toBe(4);
});

it('dispatches FetchEmailDiskUsageJob after FetchServerDataJob runs', function () {
    Bus::fake([FetchEmailDiskUsageJob::class]);

    $server = Server::factory()->create(['token' => 'valid-token']);

    $fake = new WhmApiFake;
    $this->app->instance(WhmApi::class, $fake);

    dispatch(new \App\Jobs\FetchServerDataJob($server));

    Bus::assertDispatched(FetchEmailDiskUsageJob::class, fn ($job) => $job->server->is($server));
});
