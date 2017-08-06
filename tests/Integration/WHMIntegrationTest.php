<?php

namespace Tests\Integration;

use App\Exceptions\Server\ForbiddenAccessException;
use App\Exceptions\Server\ServerConnectionException;
use App\Exceptions\Server\InvalidServerTypeException;
use App\Exceptions\Server\MissingTokenException;
use App\RemoteServer\WHM;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WHMIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_server_with_the_wrong_server_type_throws_an_exception()
    {
        $server = create('App\Server', [
            'server_type' => 'reseller'
        ]);

        try {
            $api = WHM::create($server);
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
            $api = WHM::create($server);
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
            $api = WHM::create($server);
            $api->setTimeout(3);
            $diskUsage = $api->getDiskUsage();
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
            'address' => '50.116.77.25',
            'port' => '2087',
            'server_type' => 'vps',
            'token' => 'invalid-api-token'
        ]);

        try {
            $api = WHM::create($server);
            $api->setTimeout(3);
            $diskUsage = $api->getDiskUsage();
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
            'address' => '50.116.77.25',
            'port' => '2087',
            'server_type' => 'vps',
            'token' => ''
        ]);

        $api = WHM::create($server);
        $diskUsage = $api->getDiskUsage();

        $this->assertNotEmpty($diskUsage['used']);
        $this->assertNotEmpty($diskUsage['available']);
        $this->assertNotEmpty($diskUsage['total']);
        $this->assertNotEmpty($diskUsage['percentage']);
    }
}
