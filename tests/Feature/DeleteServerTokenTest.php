<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteServerTokenTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_cannot_delete_a_server_token()
    {
        $server = create('App\Server', [
            'token' => 'valid-api-token'
        ]);

        $response = $this->deleteJson("/servers/{$server->id}/token");

        $response->assertStatus(401);
        $this->assertEquals('valid-api-token', $server->fresh()->token);
    }

    /** @test */
    function an_authorized_user_can_delete_a_server_token()
    {
        $this->signIn();

        $server = create('App\Server', [
            'token' => 'server-api-token',
        ]);

        $response = $this->deleteJson("/servers/{$server->id}/token");

        $response->assertStatus(200);

        tap($server->fresh(), function ($server) {
            $this->assertNull($server->token);
        });
    }
}
