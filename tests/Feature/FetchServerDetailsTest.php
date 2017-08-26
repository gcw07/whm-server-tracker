<?php

namespace Tests\Feature;

use App\Connectors\FakeServerConnector;
use App\Connectors\ServerConnector;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FetchServerDetailsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_can_not_fetch_server_details()
    {
        $server = create('App\Server');

        $response = $this->get("/servers/{$server->id}/fetch-details");

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function an_authorized_user_can_fetch_server_details()
    {
        $this->signIn();

        $server = create('App\Server', [
            'name'             => 'my-server-name',
            'address'          => '1.1.1.1',
            'port'             => 1000,
            'server_type'      => 'vps',
            'token'            => 'valid-server-api-token',
        ]);

        $fake = new FakeServerConnector;
        $this->app->instance(ServerConnector::class, $fake);

        $response = $this->get("/servers/{$server->id}/fetch-details");

        $response->assertStatus(200);

        $response->assertJson(['address' => '1.1.1.1']);

        tap($server->fresh(), function ($server) {
            $this->assertNotNull($server->getSetting('disk_used'));
            $this->assertNotNull($server->getSetting('disk_available'));
            $this->assertNotNull($server->getSetting('disk_total'));
            $this->assertNotNull($server->getSetting('disk_percentage'));
            $this->assertNotNull($server->getSetting('disk_last_updated'));
            $this->assertNotNull($server->getSetting('backup_enabled'));
            $this->assertNotNull($server->getSetting('backup_days'));
            $this->assertNotNull($server->getSetting('backup_retention'));
            $this->assertNotNull($server->getSetting('backup_last_updated'));
        });
    }
}
