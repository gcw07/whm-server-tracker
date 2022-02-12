<?php

use App\Connectors\WHMServerConnector;
use App\Exceptions\Server\ForbiddenAccessException;
use App\Exceptions\Server\InvalidServerTypeException;
use App\Exceptions\Server\MissingTokenException;
use App\Exceptions\Server\ServerConnectionException;
use App\Models\Server;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    if (! canTestWHMServerConnector()) {
        $this->markTestSkipped('Skipping WHM Server Integration tests because no WHM test env variables found.');
    }

    $this->whmTestServerAddress = getenv('WHM_TEST_SERVER_ADDRESS');
    $this->whmTestServerToken = getenv('WHM_TEST_SERVER_TOKEN');

    $this->connector = new WHMServerConnector;
});

function canTestWHMServerConnector(): bool
{
    return ! (empty(getenv('WHM_TEST_SERVER_TOKEN')) or
        empty(getenv('WHM_TEST_SERVER_ADDRESS')));
}

test('a server with the wrong server type throws an exception', function () {
    $server = Server::factory()->create([
        'server_type' => 'reseller',
    ]);

    try {
        $this->connector->setServer($server);
    } catch (InvalidServerTypeException $e) {
        $this->assertEquals('reseller', $server->server_type);

        return;
    }

    $this->fail("Server still attempted to connect even with the wrong server type.");
});

test('a server with a missing api token throws an exception', function () {
    $server = Server::factory()->create([
        'server_type' => 'vps',
        'token' => null,
    ]);

    try {
        $this->connector->setServer($server);
    } catch (MissingTokenException $e) {
        $this->assertEquals('vps', $server->server_type);
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

    try {
        $this->connector->setServer($server);
        $this->connector->setTimeout(3);
        $diskUsage = $this->connector->getDiskUsage();
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

    try {
        $this->connector->setServer($server);
        $this->connector->setTimeout(3);
        $diskUsage = $this->connector->getDiskUsage();
    } catch (ForbiddenAccessException $e) {
        $this->assertEquals('invalid-api-token', $server->token);

        return;
    }

    $this->fail("Server still connected even with an invalid server api token.");
});

test('it can fetch server disk usage information', function () {
    $server = Server::factory()->create([
        'address' => $this->whmTestServerAddress,
        'port' => '2087',
        'server_type' => 'vps',
        'token' => $this->whmTestServerToken,
    ]);

    $this->connector->setServer($server);
    $diskUsage = $this->connector->getDiskUsage();

    $this->assertNotEmpty($diskUsage['used']);
    $this->assertNotEmpty($diskUsage['available']);
    $this->assertNotEmpty($diskUsage['total']);
    $this->assertNotEmpty($diskUsage['percentage']);
});

test('it can fetch server backup information', function () {
    $server = Server::factory()->create([
        'address' => $this->whmTestServerAddress,
        'port' => '2087',
        'server_type' => 'vps',
        'token' => $this->whmTestServerToken,
    ]);

    $this->connector->setServer($server);
    $backups = $this->connector->getBackups();

    $this->assertNotEmpty($backups['backupenable']);
    $this->assertNotEmpty($backups['backupdays']);
    $this->assertNotEmpty($backups['backup_daily_retention']);
});

test('it can fetch server default php version', function () {
    $server = Server::factory()->create([
        'address' => $this->whmTestServerAddress,
        'port' => '2087',
        'server_type' => 'vps',
        'token' => $this->whmTestServerToken,
    ]);

    $this->connector->setServer($server);
    $phpVersion = $this->connector->getPhpVersion();

    $this->assertNotEmpty($phpVersion);
});

test('it can fetch server account list', function () {
    $server = Server::factory()->create([
        'address' => $this->whmTestServerAddress,
        'port' => '2087',
        'server_type' => 'vps',
        'token' => $this->whmTestServerToken,
    ]);

    $this->connector->setServer($server);
    $accounts = $this->connector->getAccounts();

    $this->assertGreaterThan(0, sizeof($accounts));
});

test('it can fetch server system load average', function () {
    $server = Server::factory()->create([
        'address' => $this->whmTestServerAddress,
        'port' => '2087',
        'server_type' => 'vps',
        'token' => $this->whmTestServerToken,
    ]);

    $this->connector->setServer($server);
    $load = $this->connector->getSystemLoadAvg();

    $this->assertNotEmpty($load['one']);
});
