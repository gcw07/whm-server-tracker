<?php

namespace Tests\Feature;

use App\Enums\ServerTypeEnum;
use App\Models\Server;
use App\Models\User;
use Tests\Factories\UserFactory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateServerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = UserFactory::new()->create();
    }

    private function validParams($overrides = [])
    {
        return array_merge([
            'name'        => 'my-server-name',
            'address'     => '127.0.0.1',
            'port'        => 2087,
            'server_type' => ServerTypeEnum::VPS(),
            'notes'       => 'a server note',
            'token'       => 'server-api-token'
        ], $overrides);
    }

    /** @test */
    public function guests_cannot_view_the_add_server_form()
    {
        $this->get(route('servers.create'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function an_authorized_user_can_view_the_add_server_form()
    {
        $this->actingAs($this->user)
            ->get(route('servers.create'))
            ->assertSuccessful();
    }

    /** @test */
    public function guests_cannot_add_new_servers()
    {
        $response = $this->postJson(route('servers.store'), $this->validParams());

        $response->assertStatus(401);
        $this->assertEquals(0, Server::count());
    }

    /** @test */
    public function an_authorized_user_can_add_a_valid_server()
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('servers.store'), $this->validParams([
            'name'        => 'My Test Server',
            'address'     => '255.1.1.100',
            'port'        => 1111,
            'server_type' => ServerTypeEnum::DEDICATED(),
            'notes'       => 'some server note',
            'token'       => 'new-server-api-token'
        ]));

        $response->assertRedirect(route('servers.index'));

        $this->assertDatabaseHas('servers', [
            'name' => 'My Test Server',
            'address' => '255.1.1.100',
        ]);
    }

    /**
     * @dataProvider validationDataProvider
     * @test
     * @param $field
     * @param $value
     * @param $expectedResultType
     * @param $errorMessage
     */
    public function validate_rules_for_server_create($field, $value, $expectedResultType, $errorMessage)
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('servers.store'), $this->validParams([
                $field => $value,
            ]));

        if ($expectedResultType === 'invalid') {
            $response->assertStatus(422);
            $response->assertJsonValidationErrors([$field => $errorMessage]);
            $this->assertEquals(0, Server::count());
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
            'server token is optional' => ['token', '', 'success', null],
        ];
    }
}
