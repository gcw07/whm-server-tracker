<?php

namespace Tests\Feature;

use App\Models\Server;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Factories\ServerFactory;
use Tests\Factories\UserFactory;
use Tests\TestCase;

class UpdateServerTokenTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = UserFactory::new()->create();
    }

    /** @test */
    public function guests_cannot_edit_a_server_token()
    {
        $server = ServerFactory::new()->create(['token' => 'old-valid-api-token']);

        $this->putJson(route('servers.token', $server->id), [
            'token' => 'new-valid-api-token'
        ])->assertUnauthorized();

        $this->assertEquals('old-valid-api-token', $server->fresh()->token);
    }

    /** @test */
    public function an_authorized_user_can_edit_a_server_token()
    {
        $server = ServerFactory::new()->create(['token' => 'old-server-api-token']);

        $this->actingAs($this->user)
            ->putJson(route('servers.token', $server->id), [
                'token' => 'new-server-api-token',
            ])->assertSuccessful();

        tap($server->fresh(), function (Server $server) {
            $this->assertEquals('new-server-api-token', $server->token);
        });
    }

    /** @test */
    public function server_token_is_required()
    {
        $server = ServerFactory::new()->create(['token' => 'old-server-api-token']);

        $response = $this->actingAs($this->user)
            ->putJson(route('servers.token', $server->id), [
                'token' => '',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['token' => 'field is required']);
    }
}
