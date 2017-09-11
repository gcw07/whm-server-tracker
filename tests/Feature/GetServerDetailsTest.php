<?php

namespace Tests\Feature;

use App\Connectors\FakeServerConnector;
use App\Connectors\ServerConnector;
use App\Jobs\FetchServerDetails;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class GetServerDetailsTest extends TestCase
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
            'name'        => 'my-server-name',
            'address'     => '1.1.1.1',
            'port'        => 1000,
            'server_type' => 'vps',
            'token'       => 'valid-server-api-token',
        ]);

        Queue::fake();

        $fake = new FakeServerConnector;
        $this->app->instance(ServerConnector::class, $fake);

        $response = $this->get("/servers/{$server->id}/fetch-details");

        $response->assertStatus(200);

        Queue::assertPushed(FetchServerDetails::class, function ($job) use ($server) {
            return $job->server->is($server);
        });
    }
}
