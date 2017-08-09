<?php

namespace Tests\Feature;

use App\Connectors\FakeServerConnector;
use App\Connectors\ServerConnector;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FetchServerAccountsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_can_not_fetch_server_accounts()
    {
        $server = create('App\Server');

        $response = $this->get("/servers/{$server->id}/fetch-accounts");

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function an_authorized_user_can_fetch_server_accounts()
    {
        $this->signIn();

        $server = create('App\Server', [
            'name'             => 'my-server-name',
            'address'          => '50.116.77.25',
            'port'             => 2087,
            'server_type'      => 'vps',
            'token'            => 'XAT5VTR67T56U28XKGXF8J21OAOYIRFF',
            'disk_used'        => null,
            'disk_available'   => null,
            'disk_total'       => null,
            'disk_percentage'  => null,
            'backup_enabled'   => null,
            'backup_days'      => null,
            'backup_retention' => null
        ]);

        $fake = new FakeServerConnector;
        $this->app->instance(ServerConnector::class, $fake);

        $response = $this->get("/servers/{$server->id}/fetch-accounts");

        $response->assertStatus(200);

        $response->assertJson(['address' => '1.1.1.1']);

        tap($server->fresh(), function ($server) {
            $this->assertNotNull($server->disk_used);
            $this->assertNotNull($server->disk_available);
            $this->assertNotNull($server->disk_total);
            $this->assertNotNull($server->disk_percentage);
            $this->assertNotNull($server->disk_last_updated);
            $this->assertNotNull($server->backup_enabled);
            $this->assertNotNull($server->backup_days);
            $this->assertNotNull($server->backup_retention);
            $this->assertNotNull($server->backup_last_updated);
        });
    }
}
