<?php

namespace Tests\Integration;

use App\Connectors\WHMServerConnector;
use App\Exceptions\Server\ForbiddenAccessException;
use App\Exceptions\Server\ServerConnectionException;
use App\Exceptions\Server\InvalidServerTypeException;
use App\Exceptions\Server\MissingTokenException;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WHMIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected $whmTestServerAddress;
    protected $whmTestServerToken;
    protected $connector;

    public function setUp() : void
    {
        parent::setUp();

        if (! $this->canTestWHMServerConnector()) {
            $this->markTestSkipped('Skipping WHM Server Integration tests because no WHM test env variables found.');
        }

        $this->whmTestServerAddress = getenv('WHM_TEST_SERVER_ADDRESS');
        $this->whmTestServerToken = getenv('WHM_TEST_SERVER_TOKEN');

        $this->connector = new WHMServerConnector;
    }

    /** @test */
    public function a_server_with_the_wrong_server_type_throws_an_exception()
    {
        $server = create('App\Server', [
            'server_type' => 'reseller'
        ]);

        try {
            $this->connector->setServer($server);
        } catch (InvalidServerTypeException $e) {
            $this->assertEquals('reseller', $server->server_type);
            return;
        }

        $this->fail("Server still attempted to connect even with the wrong server type.");
    }

    /** @test */
    public function a_server_with_a_missing_api_token_throws_an_exception()
    {
        $server = create('App\Server', [
            'server_type' => 'vps',
            'token' => null
        ]);

        try {
            $this->connector->setServer($server);
        } catch (MissingTokenException $e) {
            $this->assertEquals('vps', $server->server_type);
            $this->assertNull($server->token);
            return;
        }

        $this->fail("Server still attempted to connect even with a missing api token.");
    }

    /** @test */
    public function a_server_with_an_incorrect_address_throws_an_exception()
    {
        $server = create('App\Server', [
            'address' => 'invalid-address',
            'port' => '2087',
            'server_type' => 'vps',
            'token' => 'valid-api-token'
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
    }

    /** @test */
    public function a_server_with_an_invalid_api_token_throws_an_exception()
    {
        $server = create('App\Server', [
            'address' => $this->whmTestServerAddress,
            'port' => '2087',
            'server_type' => 'vps',
            'token' => 'invalid-api-token'
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
    }

    /** @test */
    public function it_can_fetch_server_disk_usage_information()
    {
        $server = create('App\Server', [
            'address' => $this->whmTestServerAddress,
            'port' => '2087',
            'server_type' => 'vps',
            'token' => $this->whmTestServerToken
        ]);

        $this->connector->setServer($server);
        $diskUsage = $this->connector->getDiskUsage();

        $this->assertNotEmpty($diskUsage['used']);
        $this->assertNotEmpty($diskUsage['available']);
        $this->assertNotEmpty($diskUsage['total']);
        $this->assertNotEmpty($diskUsage['percentage']);
    }

    /** @test */
    public function it_can_fetch_server_backup_information()
    {
        $server = create('App\Server', [
            'address' => $this->whmTestServerAddress,
            'port' => '2087',
            'server_type' => 'vps',
            'token' => $this->whmTestServerToken
        ]);

        $this->connector->setServer($server);
        $backups = $this->connector->getBackups();

        $this->assertNotEmpty($backups['backupenable']);
        $this->assertNotEmpty($backups['backupdays']);
        $this->assertNotEmpty($backups['backup_daily_retention']);
    }

    /** @test */
    public function it_can_fetch_server_default_php_version()
    {
        $server = create('App\Server', [
            'address' => $this->whmTestServerAddress,
            'port' => '2087',
            'server_type' => 'vps',
            'token' => $this->whmTestServerToken
        ]);

        $this->connector->setServer($server);
        $phpVersion = $this->connector->getPhpVersion();

        $this->assertNotEmpty($phpVersion);
    }

    /** @test */
    public function it_can_fetch_server_account_list()
    {
        $server = create('App\Server', [
            'address' => $this->whmTestServerAddress,
            'port' => '2087',
            'server_type' => 'vps',
            'token' => $this->whmTestServerToken
        ]);

        $this->connector->setServer($server);
        $accounts = $this->connector->getAccounts();

        $this->assertGreaterThan(0, sizeof($accounts));
    }

    /** @test */
    public function it_can_fetch_server_system_load_average()
    {
        $server = create('App\Server', [
            'address' => $this->whmTestServerAddress,
            'port' => '2087',
            'server_type' => 'vps',
            'token' => $this->whmTestServerToken
        ]);

        $this->connector->setServer($server);
        $load = $this->connector->getSystemLoadAvg();

        $this->assertNotEmpty($load['one']);
    }

    public function canTestWHMServerConnector()
    {
        return ! (empty(getenv('WHM_TEST_SERVER_TOKEN')) or
            empty(getenv('WHM_TEST_SERVER_ADDRESS')));
    }
}
