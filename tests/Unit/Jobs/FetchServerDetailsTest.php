<?php

use App\Jobs\FetchAccountDetailsJob;
use App\Jobs\FetchEmailDiskUsageJob;
use App\Jobs\FetchServerDetailsJob;
use App\Models\Server;
use App\Services\WHM\WhmServerDetails;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\Factories\WhmServerDetailsFake;

uses(LazilyRefreshDatabase::class);

it('fetches server details', function () {
    $server = Server::factory()->create([
        'name' => 'my-server-name',
        'address' => '1.1.1.1',
        'port' => 2087,
        'server_type' => 'vps',
        'token' => 'valid-server-api-token',
    ]);

    Event::fake();

    $fake = new WhmServerDetailsFake;
    $this->app->instance(WhmServerDetails::class, $fake);

    dispatch(new FetchServerDetailsJob($server));

    tap($server->fresh(), function (Server $server) {
        $this->assertEquals('my-server-name', $server->name);

        $this->assertEquals('100000000', $server->settings->get('disk_used'));
        $this->assertEquals('200000000', $server->settings->get('disk_available'));
        $this->assertEquals('300000000', $server->settings->get('disk_total'));
        $this->assertEquals('33', $server->settings->get('disk_percentage'));
        $this->assertEquals('1', $server->settings->get('backup_enabled'));
        $this->assertEquals('1', $server->settings->get('backup_daily_enabled'));
        $this->assertEquals('10', $server->settings->get('backup_daily_retention'));
        $this->assertEquals('0,2,4,6', $server->settings->get('backup_daily_days'));
        $this->assertEquals('1', $server->settings->get('backup_weekly_enabled'));
        $this->assertEquals('10', $server->settings->get('backup_weekly_retention'));
        $this->assertEquals('2', $server->settings->get('backup_weekly_day'));
        $this->assertEquals('1', $server->settings->get('backup_monthly_enabled'));
        $this->assertEquals('3', $server->settings->get('backup_monthly_retention'));
        $this->assertEquals('1,15', $server->settings->get('backup_monthly_days'));
        $this->assertEquals('ea-php80', $server->settings->get('php_system_version'));
        $this->assertCount(2, $server->settings->get('php_installed_versions'));
        $this->assertEquals('11.100.0.9999', $server->settings->get('whm_version'));
    });
});

it('fetches server accounts', function () {
    $server = Server::factory()->create([
        'name' => 'my-server-name',
        'address' => '1.1.1.1',
        'port' => 1000,
        'server_type' => 'vps',
        'token' => 'valid-server-api-token',
    ]);

    Event::fake();

    $fake = new WhmServerDetailsFake;
    $this->app->instance(WhmServerDetails::class, $fake);

    dispatch(new FetchServerDetailsJob($server));

    tap($server->fresh(), function (Server $server) {
        $this->assertEquals('my-server-name', $server->name);

        $this->assertCount(2, $server->accounts);
    });
});

it('fetches server accounts when there are no accounts on remote server', function () {
    $server = Server::factory()->create([
        'name' => 'my-server-name',
        'address' => '1.1.1.1',
        'port' => 1000,
        'server_type' => 'vps',
        'token' => 'valid-server-api-token',
    ]);

    Event::fake();

    $fake = new class extends WhmServerDetailsFake
    {
        protected function getAccountsData(): array
        {
            return [];
        }
    };

    $this->app->instance(WhmServerDetails::class, $fake);

    dispatch(new FetchServerDetailsJob($server));

    tap($server->fresh(), function (Server $server) {
        $this->assertEquals('my-server-name', $server->name);

        $this->assertCount(0, $server->accounts);
    });
});

it('dispatches FetchEmailDiskUsageJob after FetchServerDetailsJob runs', function () {
    Bus::fake([FetchEmailDiskUsageJob::class]);

    $server = Server::factory()->create(['token' => 'valid-token']);

    $fake = new WhmServerDetailsFake;
    $this->app->instance(WhmServerDetails::class, $fake);

    dispatch(new FetchServerDetailsJob($server));

    Bus::assertDispatched(FetchEmailDiskUsageJob::class, fn ($job) => $job->server->is($server));
});

it('dispatches FetchAccountDetailsJob after FetchServerDetailsJob runs', function () {
    Bus::fake([FetchEmailDiskUsageJob::class, FetchAccountDetailsJob::class]);

    $server = Server::factory()->create(['token' => 'valid-token']);

    $fake = new WhmServerDetailsFake;
    $this->app->instance(WhmServerDetails::class, $fake);

    dispatch(new FetchServerDetailsJob($server));

    Bus::assertDispatched(FetchAccountDetailsJob::class, fn ($job) => $job->server->is($server));
});
