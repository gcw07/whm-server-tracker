<?php

use App\Enums\ServerTypeEnum;
use App\Models\Server;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\Factories\ServerRequestDataFactory;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->requestData = ServerRequestDataFactory::new();
});

test('guests cannot view the add server form', function () {
    $this->get(route('servers.create'))
        ->assertRedirect(route('login'));
});

test('an authorized user can view the add server form', function () {
    $this->actingAs($this->user)
        ->get(route('servers.create'))
        ->assertSuccessful();
});

test('guests cannot add new servers', function () {
    $response = $this->postJson(route('servers.store'), $this->requestData->create());

    $response->assertStatus(401);
    $this->assertEquals(0, Server::count());
});

test('an authorized user can add a valid server', function () {
    $response = $this->actingAs($this->user)
        ->postJson(route('servers.store'), $this->requestData->create([
            'name' => 'My Test Server',
            'address' => '255.1.1.100',
            'port' => 1111,
            'server_type' => ServerTypeEnum::Dedicated,
            'notes' => 'some server note',
            'token' => 'new-server-api-token',
        ]));

    $response->assertRedirect(route('servers.index'));

    $this->assertDatabaseHas('servers', [
        'name' => 'My Test Server',
        'address' => '255.1.1.100',
    ]);
});

it('validates rules for create server form', function ($data) {
    // This could be fixed in future pest version.
    $field = $data[0];
    $value = $data[1];
    $expectedResultType = $data[2];
    $errorMessage = $data[3];

    $response = $this->actingAs($this->user)
        ->postJson(route('servers.store'), $this->requestData->create([
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
})->with([
    fn () => ['name', '', 'invalid', 'field is required'],
    fn () => ['address', '', 'invalid', 'field is required'],
    fn () => ['port', '', 'invalid', 'field is required'],
    fn () => ['port', 'not-a-number', 'invalid', 'must be a number'],
    fn () => ['server_type', '', 'invalid', 'field is required'],
    fn () => ['server_type', 'not-valid-type', 'invalid', 'field is not a valid'],
    fn () => ['notes', '', 'success', null],
    fn () => ['token', '', 'success', null],
]);
