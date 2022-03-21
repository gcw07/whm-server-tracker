<?php

use App\Exceptions\Server\ForbiddenAccessException;
use App\Exceptions\Server\MissingTokenException;
use App\Exceptions\Server\ServerConnectionException;
use App\Models\Server;
use App\Services\WHM\WhmApi;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    if (! canTestWHMServerConnector()) {
        $this->markTestSkipped('Skipping WHM Server Integration tests because no WHM test env variables found.');
    }

    $this->whmTestServerAddress = getenv('WHM_TEST_SERVER_ADDRESS');
    $this->whmTestServerToken = getenv('WHM_TEST_SERVER_TOKEN');

    $this->whmApi = new WhmApi();
});

function canTestWHMServerConnector(): bool
{
    return ! (empty(getenv('WHM_TEST_SERVER_TOKEN')) ||
        empty(getenv('WHM_TEST_SERVER_ADDRESS')));
}

test('a server with a missing api token throws an exception', function () {
    $server = Server::factory()->create([
        'server_type' => 'vps',
        'token' => null,
    ]);

    try {
        $this->whmApi->setServer($server);
        $this->whmApi->fetch();
    } catch (MissingTokenException $e) {
        $this->assertEquals('vps', $server->server_type->value);
        $this->assertNull($server->token);

        return;
    }

    $this->fail("Server still attempted to connect even with a missing api token.");
});

test('a server with an incorrect address throws an exception', function () {
    $server = Server::factory()->create([
        'address' => 'invalid-address',
        'port' => '2087',
        'server_type' => 'vps',
        'token' => 'valid-api-token',
    ]);

    Config::set('server-tracker.whm.connection_timeout', 3);

    try {
        $this->whmApi->setServer($server);
        $this->whmApi->fetch();
    } catch (ServerConnectionException $e) {
        $this->assertEquals('invalid-address', $server->address);

        return;
    }

    $this->fail("Server still connected even with an invalid server address.");
});

test('a server with an invalid api token throws an exception', function () {
    $server = Server::factory()->create([
        'address' => $this->whmTestServerAddress,
        'port' => '2087',
        'server_type' => 'vps',
        'token' => 'invalid-api-token',
    ]);

    Config::set('server-tracker.whm.connection_timeout', 3);

    try {
        $this->whmApi->setServer($server);
        $this->whmApi->fetch();
    } catch (ForbiddenAccessException $e) {
        $this->assertEquals('invalid-api-token', $server->token);

        return;
    }

    $this->fail("Server still connected even with an invalid server api token.");
});

test('it can fetch server data', function () {
    $server = Server::factory()->create([
        'address' => $this->whmTestServerAddress,
        'port' => '2087',
        'server_type' => 'vps',
        'token' => $this->whmTestServerToken,
    ]);

    $this->whmApi->setServer($server);
    $this->whmApi->fetch();

    tap($server->fresh(), function (Server $server) {
        $this->assertNotEmpty($server->settings->get('disk_used'));
        $this->assertNotEmpty($server->settings->get('disk_available'));
        $this->assertNotEmpty($server->settings->get('disk_total'));
        $this->assertNotEmpty($server->settings->get('disk_percentage'));
        $this->assertNotEmpty($server->settings->get('backup_enabled'));
        $this->assertNotEmpty($server->settings->get('backup_daily_enabled'));
        $this->assertNotEmpty($server->settings->get('backup_daily_retention'));
        $this->assertNotEmpty($server->settings->get('backup_daily_days'));
        $this->assertNotNull($server->settings->get('backup_weekly_enabled'));
        $this->assertNotEmpty($server->settings->get('backup_weekly_retention'));
        $this->assertNotNull($server->settings->get('backup_weekly_day'));
        $this->assertNotNull($server->settings->get('backup_monthly_enabled'));
        $this->assertNotEmpty($server->settings->get('backup_monthly_retention'));
        $this->assertNotEmpty($server->settings->get('backup_monthly_days'));
        $this->assertNotEmpty($server->settings->get('php_system_version'));
        $this->assertNotEmpty($server->settings->get('php_installed_versions'));
        $this->assertNotEmpty($server->settings->get('whm_version'));
    });
});

test('it can fetch server account list', function () {
    $server = Server::factory()->create([
        'address' => $this->whmTestServerAddress,
        'port' => '2087',
        'server_type' => 'vps',
        'token' => $this->whmTestServerToken,
    ]);

    $this->whmApi->setServer($server);
    $this->whmApi->fetch();

    tap($server->fresh(), function (Server $server) {
        $this->assertGreaterThan(0, sizeof($server->accounts));
    });
});
