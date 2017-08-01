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
            'name'             => 'my-server-name',
            'address'          => '127.0.0.1',
            'port'             => 2087,
            'server_type'      => 'vps',
            'token'            => 'server-api-token',
            'disk_used'        => 10000000,
            'disk_available'   => 115000000,
            'disk_total'       => 125000000,
            'disk_percentage'  => 8,
            'backup_enabled'   => true,
            'backup_days'      => '0',
            'backup_retention' => 1
        ], $overrides);
    }

    /** @test */
    public function guests_cannot_view_the_add_server_form()
    {
        $response = $this->get('/servers/create');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function an_authorized_user_can_view_the_add_server_form()
    {
        $this->signIn();

        $response = $this->get('/servers/create');

        $response->assertStatus(200);
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
            'name'             => 'My Test Server',
            'address'          => '255.1.1.100',
            'port'             => 1111,
            'server_type'      => 'dedicated',
            'token'            => 'new-server-api-token',
            'disk_used'        => 10000000,
            'disk_available'   => 115000000,
            'disk_total'       => 125000000,
            'disk_percentage'  => 8,
            'backup_enabled'   => false,
            'backup_days'      => '0,1',
            'backup_retention' => 5
        ]));

        $response->assertJson(['name' => 'My Test Server']);
        $response->assertJson(['address' => '255.1.1.100']);
        $response->assertJson(['port' => 1111]);
        $response->assertJson(['server_type' => 'dedicated']);
        $response->assertJson(['token' => 'new-server-api-token']);
        $response->assertJson(['disk_used' => 10000000]);
        $response->assertJson(['disk_available' => 115000000]);
        $response->assertJson(['disk_total' => 125000000]);
        $response->assertJson(['disk_percentage' => 8]);
        $response->assertJson(['backup_enabled' => false]);
        $response->assertJson(['backup_days' => '0,1']);
        $response->assertJson(['backup_retention' => 5]);
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

    /** @test */
    public function server_disk_used_is_optional()
    {
        $this->signIn();

        $response = $this->postJson('/servers', $this->validParams([
            'disk_used' => '',
        ]));

        tap(Server::first(), function ($server) {
            $this->assertNull($server->disk_used);
        });
    }

    /** @test */
    public function server_disk_available_is_optional()
    {
        $this->signIn();

        $response = $this->postJson('/servers', $this->validParams([
            'disk_available' => '',
        ]));

        tap(Server::first(), function ($server) {
            $this->assertNull($server->disk_available);
        });
    }

    /** @test */
    public function server_disk_total_is_optional()
    {
        $this->signIn();

        $response = $this->postJson('/servers', $this->validParams([
            'disk_total' => '',
        ]));

        tap(Server::first(), function ($server) {
            $this->assertNull($server->disk_total);
        });
    }

    /** @test */
    public function server_disk_percentage_is_optional()
    {
        $this->signIn();

        $response = $this->postJson('/servers', $this->validParams([
            'disk_percentage' => '',
        ]));

        tap(Server::first(), function ($server) {
            $this->assertNull($server->disk_percentage);
        });
    }

    /** @test */
    public function server_backup_enabled_is_optional()
    {
        $this->signIn();

        $response = $this->postJson('/servers', $this->validParams([
            'backup_enabled' => '',
        ]));

        tap(Server::first(), function ($server) {
            $this->assertNull($server->backup_enabled);
        });
    }

    /** @test */
    public function server_backup_days_is_optional()
    {
        $this->signIn();

        $response = $this->postJson('/servers', $this->validParams([
            'backup_days' => '',
        ]));

        tap(Server::first(), function ($server) {
            $this->assertNull($server->backup_days);
        });
    }

    /** @test */
    public function server_backup_retention_is_optional()
    {
        $this->signIn();

        $response = $this->postJson('/servers', $this->validParams([
            'backup_retention' => '',
        ]));

        tap(Server::first(), function ($server) {
            $this->assertNull($server->backup_retention);
        });
    }
}
