<?php

namespace Tests\Feature;

use App\Connectors\FakeServerConnector;
use App\Connectors\ServerConnector;
use App\Jobs\FetchServerDetails;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\Factories\ServerFactory;
use Tests\Factories\UserFactory;
use Tests\TestCase;

class GetServerDetailsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_can_not_fetch_server_details()
    {
        $server = ServerFactory::new()->create();

        $this->get(route('servers.fetch-details', $server->id))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function an_authorized_user_can_fetch_server_details()
    {
        $user = UserFactory::new()->create();
        $server = ServerFactory::new()->create([
            'name'        => 'my-server-name',
            'address'     => '1.1.1.1',
            'port'        => 1000,
            'server_type' => 'vps',
            'token'       => 'valid-server-api-token',
        ]);

        Queue::fake();

        $fake = new FakeServerConnector;
        $this->app->instance(ServerConnector::class, $fake);

        $this->actingAs($user)
            ->get(route('servers.fetch-details', $server->id))
            ->assertSuccessful();

        Queue::assertPushed(FetchServerDetails::class, function ($job) use ($server) {
            return $job->server->is($server);
        });
    }
}
