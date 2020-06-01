<?php

namespace Tests\Unit\Jobs;

use App\Connectors\FakeServerConnector;
use App\Connectors\ServerConnector;
use App\Jobs\FetchServerDetails;
use App\Models\Server;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Factories\ServerFactory;
use Tests\TestCase;

class FetchServerDetailsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_fetches_server_details()
    {
        $server = ServerFactory::new()->create([
            'name'        => 'my-server-name',
            'address'     => '1.1.1.1',
            'port'        => 1000,
            'server_type' => 'vps',
            'token'       => 'valid-server-api-token',
        ]);

        $fake = new FakeServerConnector;
        $this->app->instance(ServerConnector::class, $fake);

        FetchServerDetails::dispatch($server);

        tap($server->fresh(), function (Server $server) {
            $this->assertEquals('my-server-name', $server->name);

            $this->assertNotNull($server->settings()->get('disk_used'));
            $this->assertNotNull($server->settings()->get('disk_available'));
            $this->assertNotNull($server->settings()->get('disk_total'));
            $this->assertNotNull($server->settings()->get('disk_percentage'));
            $this->assertNotNull($server->settings()->get('backup_enabled'));
            $this->assertNotNull($server->settings()->get('backup_days'));
            $this->assertNotNull($server->settings()->get('backup_retention'));
            $this->assertNotNull($server->settings()->get('php_version'));
        });
    }
}
