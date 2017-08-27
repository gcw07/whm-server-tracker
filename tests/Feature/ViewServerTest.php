<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewServerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_can_not_view_server_details_page()
    {
        $server = create('App\Server');

        $response = $this->get("/servers/{$server->id}");

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function an_authorized_user_can_view_server_details_page()
    {
        $this->signIn();

        $server = create('App\Server');

        $response = $this->get("/servers/{$server->id}");

        $response->assertStatus(200);
        $this->assertTrue($response->data('server')->is($server));
    }
}
