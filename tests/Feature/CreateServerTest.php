<?php

use App\Enums\ServerTypeEnum;
use App\Http\Livewire\Server\Create as ServerCreate;
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

test('an authorized user can add a valid server', function () {
    $this->actingAs($this->user);

    $response = Livewire::test(ServerCreate::class)
        ->set('state', [
            'name' => 'My Test Server',
            'address' => '255.1.1.100',
            'port' => 1111,
            'server_type' => ServerTypeEnum::Dedicated,
            'notes' => 'some server note',
            'token' => 'new-server-api-token',
        ])
        ->call('save');

    $server = Server::first();

    $response->assertRedirect(route('servers.show', $server->id));

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

    $response = Livewire::test(ServerCreate::class)
        ->set('state', $this->requestData->create([$field => $value,]))
        ->call('save');

    if ($expectedResultType === 'invalid') {
        $response->assertHasErrors(["state.$field" => $errorMessage]);
        $this->assertEquals(0, Server::count());
    } else {
        tap(Server::first(), function (Server $server) use ($field) {
            $this->assertEmpty($server->{$field});
        });
    }
})->with([
    fn () => ['name', '', 'invalid', 'required'],
    fn () => ['address', '', 'invalid', 'required'],
    fn () => ['port', '', 'invalid', 'required'],
    fn () => ['port', 'not-a-number', 'invalid', 'numeric'],
    fn () => ['server_type', '', 'invalid', 'required'],
    fn () => ['server_type', 'not-valid-type', 'invalid', 'Illuminate\Validation\Rules\Enum'],
    fn () => ['notes', '', 'success', null],
    fn () => ['token', '', 'success', null],
]);
