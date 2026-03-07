<?php

use App\Enums\SslVhostTypeEnum;
use App\Jobs\FetchEmailDiskUsageJob;
use App\Jobs\FetchSslVhostsJob;
use App\Models\Account;
use App\Models\AccountSslCertificate;
use App\Models\Server;
use App\Services\WHM\WhmApi;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\Factories\WhmApiFake;

uses(LazilyRefreshDatabase::class);

it('stores ssl certificate records for each account vhost', function () {
    $server = Server::factory()->create(['token' => 'valid-token']);
    Account::factory()->create(['server_id' => $server->id, 'user' => 'mysite']);
    Account::factory()->create(['server_id' => $server->id, 'user' => 'super']);

    $fake = new WhmApiFake;
    $this->app->instance(WhmApi::class, $fake);

    dispatch(new FetchSslVhostsJob($server));

    expect(AccountSslCertificate::count())->toBe(3);
});

it('correctly maps ssl certificate fields', function () {
    $server = Server::factory()->create(['token' => 'valid-token']);
    Account::factory()->create(['server_id' => $server->id, 'user' => 'mysite']);

    $fake = new WhmApiFake;
    $this->app->instance(WhmApi::class, $fake);

    dispatch(new FetchSslVhostsJob($server));

    $cert = AccountSslCertificate::where('servername', 'my-site.com')->first();
    expect($cert)->not->toBeNull();
    expect($cert->user)->toBe('mysite');
    expect($cert->type)->toBe(SslVhostTypeEnum::Main);
    expect($cert->domains)->toBe(['my-site.com', 'www.my-site.com']);
    expect($cert->expires_at)->not->toBeNull();
    expect($cert->issuer)->toBe("Let's Encrypt");
});

it('distinguishes main and sub type correctly', function () {
    $server = Server::factory()->create(['token' => 'valid-token']);
    Account::factory()->create(['server_id' => $server->id, 'user' => 'mysite']);

    $fake = new WhmApiFake;
    $this->app->instance(WhmApi::class, $fake);

    dispatch(new FetchSslVhostsJob($server));

    expect(AccountSslCertificate::where('servername', 'my-site.com')->value('type'))->toBe(SslVhostTypeEnum::Main);
    expect(AccountSslCertificate::where('servername', 'sub.my-site.com')->value('type'))->toBe(SslVhostTypeEnum::Sub);
});

it('removes stale ssl certificate records no longer in api response', function () {
    $server = Server::factory()->create(['token' => 'valid-token']);
    $account = Account::factory()->create(['server_id' => $server->id, 'user' => 'mysite']);

    AccountSslCertificate::create([
        'account_id' => $account->id,
        'user' => 'mysite',
        'type' => 'main',
        'servername' => 'stale-old.com',
        'domains' => ['stale-old.com'],
        'expires_at' => null,
        'issuer' => null,
    ]);

    $fake = new WhmApiFake;
    $this->app->instance(WhmApi::class, $fake);

    dispatch(new FetchSslVhostsJob($server));

    expect(AccountSslCertificate::where('servername', 'stale-old.com')->exists())->toBeFalse();
    expect(AccountSslCertificate::where('servername', 'my-site.com')->exists())->toBeTrue();
});

it('stores ssl certificates for multiple accounts on the same server', function () {
    $server = Server::factory()->create(['token' => 'valid-token']);
    Account::factory()->create(['server_id' => $server->id, 'user' => 'mysite']);
    Account::factory()->create(['server_id' => $server->id, 'user' => 'super']);

    $fake = new WhmApiFake;
    $this->app->instance(WhmApi::class, $fake);

    dispatch(new FetchSslVhostsJob($server));

    $mysiteCerts = AccountSslCertificate::whereHas('account', fn ($q) => $q->where('user', 'mysite'))->count();
    $superCerts = AccountSslCertificate::whereHas('account', fn ($q) => $q->where('user', 'super'))->count();

    expect($mysiteCerts)->toBe(2);
    expect($superCerts)->toBe(1);
});

it('dispatches FetchSslVhostsJob after FetchServerDataJob runs', function () {
    Bus::fake([FetchEmailDiskUsageJob::class, FetchSslVhostsJob::class]);

    $server = Server::factory()->create(['token' => 'valid-token']);

    $fake = new WhmApiFake;
    $this->app->instance(WhmApi::class, $fake);

    dispatch(new \App\Jobs\FetchServerDataJob($server));

    Bus::assertDispatched(FetchSslVhostsJob::class, fn ($job) => $job->server->is($server));
});
