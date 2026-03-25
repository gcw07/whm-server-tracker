<?php

use App\Jobs\FetchEmailDiskUsageJob;
use App\Jobs\FetchServerDetailsJob;
use App\Models\Account;
use App\Models\AccountEmail;
use App\Models\Server;
use App\Services\WHM\DataProcessors\ProcessAccountEmails;
use App\Services\WHM\WhmEmailDiskUsage;
use App\Services\WHM\WhmServerDetails;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\Factories\WhmEmailDiskUsageFake;
use Tests\Factories\WhmServerDetailsFake;

uses(LazilyRefreshDatabase::class);

it('stores email disk usage records for each account', function () {
    $server = Server::factory()->create(['token' => 'valid-token']);

    Account::factory()->create([
        'server_id' => $server->id,
        'user' => 'mysite',
        'domain' => 'mysite.com',
    ]);

    $fake = new WhmEmailDiskUsageFake;
    $this->app->instance(WhmEmailDiskUsage::class, $fake);

    dispatch(new FetchEmailDiskUsageJob($server));

    $emails = AccountEmail::all();
    expect($emails)->toHaveCount(3);
    expect($emails->pluck('email')->toArray())->toContain('info@mysite.com', 'admin@mysite.com', 'mysite');
});

it('correctly maps email disk usage fields', function () {
    $server = Server::factory()->create(['token' => 'valid-token']);
    Account::factory()->create(['server_id' => $server->id, 'user' => 'mysite']);

    $fake = new WhmEmailDiskUsageFake;
    $this->app->instance(WhmEmailDiskUsage::class, $fake);

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
    expect($admin->disk_quota)->toBe(0);
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

    $fake = new WhmEmailDiskUsageFake;
    $this->app->instance(WhmEmailDiskUsage::class, $fake);

    dispatch(new FetchEmailDiskUsageJob($server));

    expect(AccountEmail::where('email', 'stale@mysite.com')->exists())->toBeFalse();
    expect(AccountEmail::where('email', 'info@mysite.com')->exists())->toBeTrue();
});

it('handles accounts with no regular email accounts gracefully', function () {
    $server = Server::factory()->create(['token' => 'valid-token']);
    Account::factory()->create(['server_id' => $server->id, 'user' => 'mysite']);

    $fake = new class extends WhmEmailDiskUsageFake
    {
        protected function getEmailDiskUsageData(string $username): array
        {
            return ['result' => ['data' => []]];
        }
    };

    $this->app->instance(WhmEmailDiskUsage::class, $fake);

    dispatch(new FetchEmailDiskUsageJob($server));

    expect(AccountEmail::count())->toBe(1);
});

it('stores email records for multiple accounts', function () {
    $server = Server::factory()->create(['token' => 'valid-token']);
    Account::factory()->create(['server_id' => $server->id, 'user' => 'mysite']);
    Account::factory()->create(['server_id' => $server->id, 'user' => 'super']);

    $fake = new WhmEmailDiskUsageFake;
    $this->app->instance(WhmEmailDiskUsage::class, $fake);

    dispatch(new FetchEmailDiskUsageJob($server));

    expect(AccountEmail::count())->toBe(6);
});

it('correctly maps default email disk usage fields', function () {
    $server = Server::factory()->create(['token' => 'valid-token']);
    Account::factory()->create(['server_id' => $server->id, 'user' => 'mysite', 'domain' => 'mysite.com']);

    $fake = new WhmEmailDiskUsageFake;
    $this->app->instance(WhmEmailDiskUsage::class, $fake);

    dispatch(new FetchEmailDiskUsageJob($server));

    $default = AccountEmail::where('email', 'mysite')->first();
    expect($default)->not->toBeNull();
    expect($default->user)->toBe('system');
    expect($default->domain)->toBe('mysite.com');
    expect($default->disk_used)->toBe(2048000);
    expect($default->disk_quota)->toBe(0);
    expect($default->disk_used_percent)->toBe(0.0);
    expect($default->suspended_incoming)->toBeFalse();
    expect($default->suspended_login)->toBeFalse();
});

it('stores regular emails when main email account API fails', function () {
    $server = Server::factory()->create(['token' => 'valid-token']);
    Account::factory()->create(['server_id' => $server->id, 'user' => 'mysite', 'domain' => 'mysite.com']);

    $fake = new class extends WhmEmailDiskUsageFake
    {
        public function fetch(): void
        {
            $accounts = $this->server->accounts()->get();

            foreach ($accounts as $account) {
                $data = $this->getEmailDiskUsageData($account->user);
                (new ProcessAccountEmails)->execute($account, $data);
            }
        }
    };

    $this->app->instance(WhmEmailDiskUsage::class, $fake);

    dispatch(new FetchEmailDiskUsageJob($server));

    expect(AccountEmail::count())->toBe(2);
    expect(AccountEmail::where('email', 'info@mysite.com')->exists())->toBeTrue();
    expect(AccountEmail::where('email', 'mysite')->exists())->toBeFalse();
});

it('dispatches FetchEmailDiskUsageJob after FetchServerDetailsJob runs', function () {
    Bus::fake([FetchEmailDiskUsageJob::class]);

    $server = Server::factory()->create(['token' => 'valid-token']);

    $fake = new WhmServerDetailsFake;
    $this->app->instance(WhmServerDetails::class, $fake);

    dispatch(new FetchServerDetailsJob($server));

    Bus::assertDispatched(FetchEmailDiskUsageJob::class, fn ($job) => $job->server->is($server));
});
