<?php

namespace Tests\Feature;

use App\Server;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteServerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_cannot_delete_a_server()
    {
        $server = create('App\Server', [
            'name' => 'my-server-name'
        ]);

        $response = $this->deleteJson("/servers/{$server->id}");

        $response->assertStatus(401);
        $this->assertEquals('my-server-name', $server->fresh()->name);
    }

    /** @test */
    function an_authorized_user_can_delete_a_server()
    {
        $this->signIn();

        $server = create('App\Server', [
            'name' => 'my-server-name',
        ]);

        $response = $this->deleteJson("/servers/{$server->id}");

        $response->assertStatus(204);
        $this->assertEquals(0, Server::count());
    }
}
