<?php

namespace Tests\Feature;

use App\Models\Server;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Factories\ServerFactory;
use Tests\Factories\UserFactory;
use Tests\TestCase;

class DeleteServerTokenTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Server $server;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = UserFactory::new()->create();
        $this->server = ServerFactory::new()->create([
            'token' => 'valid-api-token'
        ]);
    }

    /** @test */
    public function guests_cannot_delete_a_server_token()
    {
        $this->deleteJson(route('servers.token-destroy', $this->server->id))
            ->assertUnauthorized();

        $this->assertEquals('valid-api-token', $this->server->fresh()->token);
    }

    /** @test */
    public function an_authorized_user_can_delete_a_server_token()
    {
        $this->actingAs($this->user)
            ->deleteJson((route('servers.token-destroy', $this->server->id)))
            ->assertSuccessful();

        tap($this->server->fresh(), function (Server $server) {
            $this->assertNull($server->token);
        });
    }
}
