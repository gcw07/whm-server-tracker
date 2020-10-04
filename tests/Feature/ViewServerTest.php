<?php

namespace Tests\Feature;

use App\Models\Server;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewServerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_can_not_view_server_details_page()
    {
        $server = Server::factory()->create();

        $this->get(route('servers.show', $server->id))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function an_authorized_user_can_view_server_details_page()
    {
        $user = User::factory()->create();
        $server = Server::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('servers.show', $server->id))
            ->assertSuccessful();

//        $this->assertTrue($response->data('server')->is($server));
    }
}
