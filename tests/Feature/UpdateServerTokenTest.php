<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateServerTokenTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_cannot_edit_a_server_token()
    {
        $server = create('App\Server', [
            'token' => 'old-valid-api-token'
        ]);

        $response = $this->putJson("/servers/{$server->id}/token", [
            'token' => 'new-valid-api-token'
        ]);

        $response->assertStatus(401);
        $this->assertEquals('old-valid-api-token', $server->fresh()->token);
    }

    /** @test */
    function an_authorized_user_can_edit_a_server_token()
    {
        $this->signIn();

        $server = create('App\Server', [
            'token' => 'old-server-api-token',
        ]);

        $response = $this->putJson("/servers/{$server->id}/token", [
            'token' => 'new-server-api-token',
        ]);

        tap($server->fresh(), function ($server) {
            $this->assertEquals('new-server-api-token', $server->token);
        });
    }

    /** @test */
    public function server_token_is_required()
    {
        $this->signIn();

        $server = create('App\Server', [
            'token' => 'old-server-api-token',
        ]);

        $response = $this->putJson("/servers/{$server->id}/token", [
            'token' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonHasErrors('token');

        tap($server->fresh(), function ($server) {
            $this->assertEquals('old-server-api-token', $server->token);
        });
    }
}
