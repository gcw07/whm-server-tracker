<?php

namespace Tests\Feature;

use App\Server;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateServerTest extends TestCase
{
    use RefreshDatabase;

    private function oldAttributes($overrides = [])
    {
        return array_merge([
            'name'             => 'old-my-server-name',
            'address'          => '1.1.1.1',
            'port'             => 1000,
            'server_type'      => 'dedicated',
            'notes'            => 'old server note',
            'disk_used'        => 10000000,
            'disk_available'   => 115000000,
            'disk_total'       => 125000000,
            'disk_percentage'  => 8,
            'backup_enabled'   => false,
            'backup_days'      => '1,2',
            'backup_retention' => 10
        ], $overrides);
    }

    private function validParams($overrides = [])
    {
        return array_merge([
            'name'             => 'new-my-server-name',
            'address'          => '192.1.1.1',
            'port'             => 2000,
            'server_type'      => 'vps',
            'notes'            => 'new server note',
            'disk_used'        => 5000000,
            'disk_available'   => 145000000,
            'disk_total'       => 150000000,
            'disk_percentage'  => 3,
            'backup_enabled'   => true,
            'backup_days'      => '0,1',
            'backup_retention' => 5
        ], $overrides);
    }

    /** @test */
    public function guests_cannot_view_the_edit_server_form()
    {
        $server = create('App\Server');

        $response = $this->get("/servers/{$server->id}/edit");

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function an_authorized_user_can_view_the_edit_server_form()
    {
        $this->signIn();
        $server = create('App\Server');

        $response = $this->get("/servers/{$server->id}/edit");

        $response->assertStatus(200);
        $this->assertTrue($response->data('server')->is($server));
    }

    /** @test */
    public function guests_cannot_edit_a_server()
    {
        $server = create('App\Server', $this->oldAttributes());

        $response = $this->putJson("/servers/{$server->id}", $this->validParams());

        $response->assertStatus(401);
        $this->assertArraySubset($this->oldAttributes(), $server->fresh()->getAttributes());
    }

    /** @test */
    function an_authorized_user_can_edit_a_server()
    {
        $this->signIn();

        $server = create('App\Server', [
            'name'             => 'old-my-server-name',
            'address'          => '1.1.1.1',
            'port'             => 1000,
            'server_type'      => 'dedicated',
            'notes'            => 'old server note',
            'disk_used'        => 10000000,
            'disk_available'   => 115000000,
            'disk_total'       => 125000000,
            'disk_percentage'  => 8,
            'backup_enabled'   => false,
            'backup_days'      => '1,2',
            'backup_retention' => 10
        ]);

        $response = $this->putJson("/servers/{$server->id}", $this->validParams([
            'name'             => 'new-my-server-name',
            'address'          => '192.1.1.1',
            'port'             => 2000,
            'server_type'      => 'vps',
            'notes'            => 'new server note',
            'disk_used'        => 5000000,
            'disk_available'   => 145000000,
            'disk_total'       => 150000000,
            'disk_percentage'  => 3,
            'backup_enabled'   => true,
            'backup_days'      => '0,1',
            'backup_retention' => 5
        ]));

        tap($server->fresh(), function ($server) {
            $this->assertEquals('new-my-server-name', $server->name);
            $this->assertEquals('192.1.1.1', $server->address);
            $this->assertEquals(2000, $server->port);
            $this->assertEquals('vps', $server->server_type);
            $this->assertEquals('new server note', $server->notes);
            $this->assertEquals(5000000, $server->disk_used);
            $this->assertEquals(145000000, $server->disk_available);
            $this->assertEquals(150000000, $server->disk_total);
            $this->assertEquals(3, $server->disk_percentage);
            $this->assertTrue($server->backup_enabled);
            $this->assertEquals('0,1', $server->backup_days);
            $this->assertEquals(5, $server->backup_retention);
        });
    }

    /** @test */
    public function the_api_token_is_cleared_when_reseller_server_type_is_selected()
    {
        $this->signIn();

        $server = create('App\Server', [
            'server_type' => 'dedicated',
            'token'       => 'old-api-token',
        ]);

        $response = $this->putJson("/servers/{$server->id}", $this->validParams([
            'server_type' => 'reseller'
        ]));

        tap($server->fresh(), function ($server) {
            $this->assertEquals('reseller', $server->server_type);
            $this->assertNull($server->token);
        });
    }

    /** @test */
    public function server_name_is_required()
    {
        $this->signIn();

        $server = create('App\Server', [
            'name' => 'old-my-server-name',
        ]);

        $response = $this->putJson("/servers/{$server->id}", $this->validParams([
            'name' => '',
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('name');

        tap($server->fresh(), function ($server) {
            $this->assertEquals('old-my-server-name', $server->name);
        });
    }

    /** @test */
    public function server_address_is_required()
    {
        $this->signIn();

        $server = create('App\Server', [
            'address' => '1.1.1.1',
        ]);

        $response = $this->putJson("/servers/{$server->id}", $this->validParams([
            'address' => '',
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('address');

        tap($server->fresh(), function ($server) {
            $this->assertEquals('1.1.1.1', $server->address);
        });
    }

    /** @test */
    public function server_port_is_required()
    {
        $this->signIn();

        $server = create('App\Server', [
            'port' => 1000,
        ]);

        $response = $this->putJson("/servers/{$server->id}", $this->validParams([
            'port' => '',
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('port');

        tap($server->fresh(), function ($server) {
            $this->assertEquals(1000, $server->port);
        });
    }

    /** @test */
    public function server_port_must_be_a_number()
    {
        $this->signIn();

        $server = create('App\Server', [
            'port' => 1000,
        ]);

        $response = $this->putJson("/servers/{$server->id}", $this->validParams([
            'port' => 'not-a-number',
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('port');

        tap($server->fresh(), function ($server) {
            $this->assertEquals(1000, $server->port);
        });
    }

    /** @test */
    public function server_type_is_required()
    {
        $this->signIn();

        $server = create('App\Server', [
            'server_type' => 'vps',
        ]);

        $response = $this->putJson("/servers/{$server->id}", $this->validParams([
            'server_type' => '',
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('server_type');

        tap($server->fresh(), function ($server) {
            $this->assertEquals('vps', $server->server_type);
        });
    }

    /** @test */
    public function server_type_must_be_a_valid_option()
    {
        $this->signIn();

        $server = create('App\Server', [
            'server_type' => 'vps',
        ]);

        $response = $this->putJson("/servers/{$server->id}", $this->validParams([
            'server_type' => 'invalid-option',
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('server_type');

        tap($server->fresh(), function ($server) {
            $this->assertEquals('vps', $server->server_type);
        });
    }


    /** @test */
    public function server_notes_is_optional()
    {
        $this->signIn();

        $server = create('App\Server', [
            'notes' => 'old server notes',
        ]);

        $response = $this->putJson("/servers/{$server->id}", $this->validParams([
            'notes' => '',
        ]));

        tap($server->fresh(), function ($server) {
            $this->assertNull($server->notes);
        });
    }

    /** @test */
    public function server_disk_used_is_optional()
    {
        $this->signIn();

        $server = create('App\Server', [
            'disk_used' => 10000,
        ]);

        $response = $this->putJson("/servers/{$server->id}", $this->validParams([
            'disk_used' => '',
        ]));

        tap($server->fresh(), function ($server) {
            $this->assertNull($server->disk_used);
        });
    }

    /** @test */
    public function server_disk_available_is_optional()
    {
        $this->signIn();

        $server = create('App\Server', [
            'disk_available' => 15000,
        ]);

        $response = $this->putJson("/servers/{$server->id}", $this->validParams([
            'disk_available' => '',
        ]));

        tap($server->fresh(), function ($server) {
            $this->assertNull($server->disk_available);
        });
    }

    /** @test */
    public function server_disk_total_is_optional()
    {
        $this->signIn();

        $server = create('App\Server', [
            'disk_total' => 150000,
        ]);

        $response = $this->putJson("/servers/{$server->id}", $this->validParams([
            'disk_total' => '',
        ]));

        tap($server->fresh(), function ($server) {
            $this->assertNull($server->disk_total);
        });
    }

    /** @test */
    public function server_disk_percentage_is_optional()
    {
        $this->signIn();

        $server = create('App\Server', [
            'disk_percentage' => 10,
        ]);

        $response = $this->putJson("/servers/{$server->id}", $this->validParams([
            'disk_percentage' => '',
        ]));

        tap($server->fresh(), function ($server) {
            $this->assertNull($server->disk_percentage);
        });
    }

    /** @test */
    public function server_backup_enabled_is_optional()
    {
        $this->signIn();

        $server = create('App\Server', [
            'backup_enabled' => true,
        ]);

        $response = $this->putJson("/servers/{$server->id}", $this->validParams([
            'backup_enabled' => '',
        ]));

        tap($server->fresh(), function ($server) {
            $this->assertNull($server->backup_enabled);
        });
    }

    /** @test */
    public function server_backup_days_is_optional()
    {
        $this->signIn();

        $server = create('App\Server', [
            'backup_days' => '0,5',
        ]);

        $response = $this->putJson("/servers/{$server->id}", $this->validParams([
            'backup_days' => '',
        ]));

        tap($server->fresh(), function ($server) {
            $this->assertNull($server->backup_days);
        });
    }

    /** @test */
    public function server_backup_retention_is_optional()
    {
        $this->signIn();

        $server = create('App\Server', [
            'backup_retention' => 7,
        ]);

        $response = $this->putJson("/servers/{$server->id}", $this->validParams([
            'backup_retention' => '',
        ]));

        tap($server->fresh(), function ($server) {
            $this->assertNull($server->backup_retention);
        });
    }
}
