<?php

namespace Tests\Feature;

use App\Server;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateServerTest extends TestCase
{
    use RefreshDatabase;

    private function validParams($overrides = [])
    {
        return array_merge([
            'name'        => 'my-server-name',
            'address'     => '127.0.0.1',
            'port'        => 2087,
            'server_type' => 'vps',
            'notes'       => 'a server note',
            'token'       => 'server-api-token'
        ], $overrides);
    }

    /** @test */
    public function guests_cannot_add_new_servers()
    {
        $response = $this->postJson('/servers', $this->validParams());

        $response->assertStatus(401);
        $this->assertEquals(0, Server::count());
    }

    /** @test */
    public function an_authorized_user_can_add_a_valid_server()
    {
        $this->signIn();

        $response = $this->postJson('/servers', $this->validParams([
            'name'        => 'My Test Server',
            'address'     => '255.1.1.100',
            'port'        => 1111,
            'server_type' => 'dedicated',
            'notes'       => 'some server note',
            'token'       => 'new-server-api-token'
        ]));

        $response->assertJson(['name' => 'My Test Server']);
        $response->assertJson(['address' => '255.1.1.100']);
        $response->assertJson(['port' => 1111]);
        $response->assertJson(['server_type' => 'dedicated']);
        $response->assertJson(['notes' => 'some server note']);
    }

    /** @test */
    public function server_name_is_required()
    {
        $this->signIn();

        $response = $this->postJson('/servers', $this->validParams([
            'name' => '',
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('name');
        $this->assertEquals(0, Server::count());
    }

    /** @test */
    public function server_address_is_required()
    {
        $this->signIn();

        $response = $this->postJson('/servers', $this->validParams([
            'address' => '',
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('address');
        $this->assertEquals(0, Server::count());
    }

    /** @test */
    public function server_port_is_required()
    {
        $this->signIn();

        $response = $this->postJson('/servers', $this->validParams([
            'port' => '',
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('port');
        $this->assertEquals(0, Server::count());
    }

    /** @test */
    public function server_port_must_be_a_number()
    {
        $this->signIn();

        $response = $this->postJson('/servers', $this->validParams([
            'port' => 'not-a-number',
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('port');
        $this->assertEquals(0, Server::count());
    }

    /** @test */
    public function server_type_is_required()
    {
        $this->signIn();

        $response = $this->postJson('/servers', $this->validParams([
            'server_type' => '',
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('server_type');
        $this->assertEquals(0, Server::count());
    }

    /** @test */
    public function server_type_must_be_a_valid_option()
    {
        $this->signIn();

        $response = $this->postJson('/servers', $this->validParams([
            'server_type' => 'invalid-option',
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('server_type');
        $this->assertEquals(0, Server::count());
    }

    /** @test */
    public function server_notes_is_optional()
    {
        $this->signIn();

        $response = $this->postJson('/servers', $this->validParams([
            'notes' => '',
        ]));

        tap(Server::first(), function ($server) {
            $this->assertNull($server->notes);
        });
    }

    /** @test */
    public function server_token_is_optional()
    {
        $this->signIn();

        $response = $this->postJson('/servers', $this->validParams([
            'token' => '',
        ]));

        tap(Server::first(), function ($server) {
            $this->assertNull($server->token);
        });
    }
}
