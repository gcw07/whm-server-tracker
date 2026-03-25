<?php

use App\Enums\SslVhostTypeEnum;
use App\Jobs\FetchAccountDetailsJob;
use App\Jobs\FetchEmailDiskUsageJob;
use App\Jobs\FetchServerDetailsJob;
use App\Models\Account;
use App\Models\AccountSslCertificate;
use App\Models\Server;
use App\Services\WHM\WhmAccountDetails;
use App\Services\WHM\WhmServerDetails;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\Factories\WhmAccountDetailsFake;
use Tests\Factories\WhmServerDetailsFake;

uses(LazilyRefreshDatabase::class);

it('stores ssl certificate records for each account vhost', function () {
    $server = Server::factory()->create(['token' => 'valid-token']);
    Account::factory()->create(['server_id' => $server->id, 'user' => 'mysite']);
    Account::factory()->create(['server_id' => $server->id, 'user' => 'super']);

    $fake = new WhmAccountDetailsFake;
    $this->app->instance(WhmAccountDetails::class, $fake);

    dispatch(new FetchAccountDetailsJob($server));

    expect(AccountSslCertificate::count())->toBe(3);
});

it('correctly maps ssl certificate fields', function () {
    $server = Server::factory()->create(['token' => 'valid-token']);
    Account::factory()->create(['server_id' => $server->id, 'user' => 'mysite', 'domain' => 'my-site.com']);

    $fake = new WhmAccountDetailsFake;
    $this->app->instance(WhmAccountDetails::class, $fake);

    dispatch(new FetchAccountDetailsJob($server));

    $cert = AccountSslCertificate::where('servername', 'my-site.com')->first();
    expect($cert)->not->toBeNull();
    expect($cert->user)->toBe('mysite');
    expect($cert->type)->toBe(SslVhostTypeEnum::Main);
    expect($cert->vhost_domains)->toBe(['my-site.com', 'www.my-site.com']);
    expect($cert->certificate_domains)->toBe(['my-site.com', 'www.my-site.com']);
    expect($cert->expires_at)->not->toBeNull();
    expect($cert->issuer)->toBe("Let's Encrypt");
});

it('distinguishes main and sub type correctly', function () {
    $server = Server::factory()->create(['token' => 'valid-token']);
    Account::factory()->create(['server_id' => $server->id, 'user' => 'mysite', 'domain' => 'my-site.com']);

    $fake = new WhmAccountDetailsFake;
    $this->app->instance(WhmAccountDetails::class, $fake);

    dispatch(new FetchAccountDetailsJob($server));

    expect(AccountSslCertificate::where('servername', 'my-site.com')->value('type'))->toBe(SslVhostTypeEnum::Main);
    expect(AccountSslCertificate::where('servername', 'sub.my-site.com')->value('type'))->toBe(SslVhostTypeEnum::Sub);
});

it('removes stale ssl certificate records no longer in api response', function () {
    $server = Server::factory()->create(['token' => 'valid-token']);
    $account = Account::factory()->create(['server_id' => $server->id, 'user' => 'mysite', 'domain' => 'my-site.com']);

    AccountSslCertificate::create([
        'account_id' => $account->id,
        'user' => 'mysite',
        'type' => 'main',
        'servername' => 'stale-old.com',
        'vhost_domains' => ['stale-old.com'],
        'certificate_domains' => [],
        'expires_at' => null,
        'issuer' => null,
    ]);

    $fake = new WhmAccountDetailsFake;
    $this->app->instance(WhmAccountDetails::class, $fake);

    dispatch(new FetchAccountDetailsJob($server));

    expect(AccountSslCertificate::where('servername', 'stale-old.com')->exists())->toBeFalse();
    expect(AccountSslCertificate::where('servername', 'my-site.com')->exists())->toBeTrue();
});

it('stores ssl certificates for multiple accounts on the same server', function () {
    $server = Server::factory()->create(['token' => 'valid-token']);
    Account::factory()->create(['server_id' => $server->id, 'user' => 'mysite', 'domain' => 'my-site.com']);
    Account::factory()->create(['server_id' => $server->id, 'user' => 'super', 'domain' => 'super-system.com']);

    $fake = new WhmAccountDetailsFake;
    $this->app->instance(WhmAccountDetails::class, $fake);

    dispatch(new FetchAccountDetailsJob($server));

    $mysiteCerts = AccountSslCertificate::whereHas('account', fn ($q) => $q->where('user', 'mysite'))->count();
    $superCerts = AccountSslCertificate::whereHas('account', fn ($q) => $q->where('user', 'super'))->count();

    expect($mysiteCerts)->toBe(2);
    expect($superCerts)->toBe(1);
});

it('stores php version for each account', function () {
    $server = Server::factory()->create(['token' => 'valid-token']);
    Account::factory()->create(['server_id' => $server->id, 'user' => 'mysite', 'domain' => 'my-site.com']);
    Account::factory()->create(['server_id' => $server->id, 'user' => 'super', 'domain' => 'super-system.com']);

    $fake = new WhmAccountDetailsFake;
    $this->app->instance(WhmAccountDetails::class, $fake);

    dispatch(new FetchAccountDetailsJob($server));

    expect(Account::where('domain', 'my-site.com')->value('php_version'))->toBe('ea-php81');
    expect(Account::where('domain', 'super-system.com')->value('php_version'))->toBe('ea-php82');
});

it('handles empty php vhost versions response gracefully', function () {
    $server = Server::factory()->create(['token' => 'valid-token']);
    Account::factory()->create(['server_id' => $server->id, 'user' => 'mysite', 'domain' => 'my-site.com']);

    $fake = new class extends WhmAccountDetailsFake
    {
        protected function getPhpVhostVersionsData(): array
        {
            return ['data' => ['versions' => []]];
        }
    };

    $this->app->instance(WhmAccountDetails::class, $fake);

    dispatch(new FetchAccountDetailsJob($server));

    expect(Account::where('domain', 'my-site.com')->value('php_version'))->toBeNull();
});

it('skips php version for accounts with no matching domain', function () {
    $server = Server::factory()->create(['token' => 'valid-token']);
    Account::factory()->create(['server_id' => $server->id, 'user' => 'mysite', 'domain' => 'my-site.com']);

    $fake = new class extends WhmAccountDetailsFake
    {
        protected function getPhpVhostVersionsData(): array
        {
            return [
                'data' => [
                    'versions' => [
                        ['vhost' => 'unknown-domain.com', 'php_version' => 'ea-php81'],
                    ],
                ],
            ];
        }
    };

    $this->app->instance(WhmAccountDetails::class, $fake);

    dispatch(new FetchAccountDetailsJob($server));

    expect(Account::where('domain', 'my-site.com')->value('php_version'))->toBeNull();
});

it('dispatches FetchAccountDetailsJob after FetchServerDetailsJob runs', function () {
    Bus::fake([FetchEmailDiskUsageJob::class, FetchAccountDetailsJob::class]);

    $server = Server::factory()->create(['token' => 'valid-token']);

    $fake = new WhmServerDetailsFake;
    $this->app->instance(WhmServerDetails::class, $fake);

    dispatch(new FetchServerDetailsJob($server));

    Bus::assertDispatched(FetchAccountDetailsJob::class, fn ($job) => $job->server->is($server));
});
