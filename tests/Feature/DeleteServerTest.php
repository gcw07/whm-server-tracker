<?php

namespace Tests\Feature;

use App\Account;
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

    /** @test */
    public function accounts_are_deleted_when_a_server_is_deleted()
    {
        $this->signIn();

        $server = create('App\Server', ['name' => 'my-server-name']);
        $otherServer = create('App\Server', ['name' => 'other-server-name']);

        $accounts = create('App\Account', ['server_id' => $server->id], 2);
        $otherAccount = create('App\Account', ['server_id' => $otherServer->id]);

        $this->assertEquals(2, Server::count());

        tap(Server::first(), function ($server) {
            $this->assertCount(2, $server->accounts);
        });

        $response = $this->deleteJson("/servers/{$server->id}");

        $response->assertStatus(204);

        $this->assertEquals(1, Server::count());
        $this->assertEquals(1, Account::count());
    }
}
