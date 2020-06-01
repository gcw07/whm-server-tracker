<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Server;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Factories\ServerFactory;
use Tests\Factories\UserFactory;
use Tests\TestCase;

class DeleteServerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Server $server;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = UserFactory::new()->create();
        $this->server = ServerFactory::new()->create();
    }

    /** @test */
    public function guests_cannot_delete_a_server()
    {
        $this->deleteJson(route('servers.destroy', $this->server->id))
            ->assertUnauthorized();

        $this->assertEquals(1, Server::count());
    }

    /** @test */
    public function an_authorized_user_can_delete_a_server()
    {
        $this->actingAs($this->user)
            ->deleteJson((route('servers.destroy', $this->server->id)))
            ->assertSuccessful();

        $this->assertEquals(0, Server::count());
    }

    /** @test */
    public function accounts_are_deleted_when_a_server_is_deleted()
    {
        $server = ServerFactory::new()
            ->with(Account::class, 'accounts', 5)
            ->create(['name' => 'my-server-name']);
        $otherServer = ServerFactory::new()
            ->with(Account::class, 'accounts', 1)
            ->create(['name' => 'other-server-name']);

        $this->assertEquals(3, Server::count());

        tap($server->fresh(), function (Server $server) {
            $this->assertCount(5, $server->accounts);
        });

        $this->actingAs($this->user)
            ->deleteJson((route('servers.destroy', $server->id)))
            ->assertSuccessful();

        $this->assertEquals(2, Server::count());
        $this->assertEquals(1, Account::count());
    }
}
