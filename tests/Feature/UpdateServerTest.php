<?php

namespace Tests\Feature;

use App\Enums\ServerTypeEnum;
use App\Models\Server;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateServerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Server $server;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->server = Server::factory()->create(['name' => 'old-my-server-name']);
    }

    private function validParams($overrides = [])
    {
        return array_merge([
            'name'        => 'new-my-server-name',
            'address'     => '192.1.1.1',
            'port'        => 2000,
            'server_type' => ServerTypeEnum::VPS(),
            'notes'       => 'new server note'
        ], $overrides);
    }

    /** @test */
    public function guests_cannot_view_the_edit_server_form()
    {
        $this->get(route('servers.edit', $this->server->id))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function an_authorized_user_can_view_the_edit_server_form()
    {
        $this->actingAs($this->user)
            ->get(route('servers.edit', $this->server->id))
            ->assertSuccessful();
    }

    /** @test */
    public function guests_cannot_edit_a_server()
    {
        $this->putJson(route('servers.update', $this->server->id), $this->validParams())
            ->assertUnauthorized();

        tap($this->server->fresh(), function (Server $server) {
            $this->assertEquals('old-my-server-name', $server->name);
        });
    }

    /** @test */
    public function an_authorized_user_can_edit_a_server()
    {
        $response = $this->actingAs($this->user)
            ->putJson(route('servers.update', $this->server->id), $this->validParams([
                'name' => 'My Server',
            ]));

        $response->assertRedirect(route('servers.index'));

        tap($this->server->fresh(), function (Server $server) {
            $this->assertEquals('My Server', $server->name);
        });
    }

    /** fix or remove */
    public function the_api_token_disk_and_backup_details_are_cleared_when_reseller_server_type_is_selected()
    {
        $this->signIn();

        $server = create('App\Server', [
            'server_type'      => 'dedicated',
            'token'            => 'old-api-token',
        ]);

        $server->settings()->merge([
            'disk_used'        => 10000000,
            'disk_available'   => 115000000,
            'disk_total'       => 125000000,
            'disk_percentage'  => 8,
            'backup_enabled'   => false,
            'backup_days'      => '1,2',
            'backup_retention' => 10
        ]);

        $response = $this->putJson("/servers/{$server->id}", $this->validParams([
            'server_type' => 'reseller'
        ]));

        tap($server->fresh(), function ($server) {
            $this->assertEquals('reseller', $server->server_type);
            $this->assertNull($server->token);
            $this->assertNull($server->settings()->disk_used);
            $this->assertNull($server->settings()->disk_available);
            $this->assertNull($server->settings()->disk_total);
            $this->assertNull($server->settings()->disk_percentage);
            $this->assertNull($server->settings()->backup_enabled);
            $this->assertNull($server->settings()->backup_days);
            $this->assertNull($server->settings()->backup_retention);
        });
    }

    /**
     * @dataProvider validationDataProvider
     * @test
     * @param $field
     * @param $value
     * @param $expectedResultType
     * @param $errorMessage
     */
    public function validate_rules_for_server_edit($field, $value, $expectedResultType, $errorMessage)
    {
        $response = $this->actingAs($this->user)
            ->putJson(route('servers.update', $this->server->id), $this->validParams([
                $field => $value,
            ]));

        if ($expectedResultType === 'invalid') {
            $response->assertStatus(422);
            $response->assertJsonValidationErrors([$field => $errorMessage]);
        } else {
            tap(Server::first(), function (Server $server) use ($field) {
                $this->assertNull($server->{$field});
            });
        }
    }

    public function validationDataProvider()
    {
        return [
            'server name is required' => ['name', '', 'invalid', 'field is required'],
            'server address is required' => ['address', '', 'invalid', 'field is required'],
            'server port is required' => ['port', '', 'invalid', 'field is required'],
            'server port must be a number' => ['port', 'not-a-number', 'invalid', 'must be a number'],
            'server type is required' => ['server_type', '', 'invalid', 'field is required'],
            'server type is valid type' => ['server_type', 'not-valid-type', 'invalid', 'field is not a valid'],
            'server notes is optional' => ['notes', '', 'success', null],
        ];
    }
}
