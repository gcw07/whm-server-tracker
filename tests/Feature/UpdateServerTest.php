<?php

use App\Http\Livewire\Server\Edit as ServerEdit;
use App\Models\Server;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\Factories\ServerRequestDataFactory;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->server = Server::factory()->create(['name' => 'old-my-server-name']);
    $this->requestData = ServerRequestDataFactory::new();
});

test('guests cannot view the edit server form', function () {
    $this->get(route('servers.edit', $this->server->id))
        ->assertRedirect(route('login'));
});

test('an authorized user can view the edit server form', function () {
    $this->actingAs($this->user)
        ->get(route('servers.edit', $this->server->id))
        ->assertSuccessful();
});

test('an authorized user can edit a server', function () {
    $this->actingAs($this->user);

    Livewire::test(ServerEdit::class, ['server' => $this->server])
        ->set('state', $this->requestData->create([
            'name' => 'My Server',
        ]))
        ->call('save')
        ->assertRedirect(route('servers.show', $this->server->id));

    tap($this->server->fresh(), function (Server $server) {
        $this->assertEquals('My Server', $server->name);
    });
});

it('validates rules for server edit form', function ($data) {
    // This could be fixed in future pest version.
    $field = $data[0];
    $value = $data[1];
    $expectedResultType = $data[2];
    $errorMessage = $data[3];

    $this->actingAs($this->user);

    $response = Livewire::test(ServerEdit::class, ['server' => $this->server])
        ->set('state', $this->requestData->create([$field => $value]))
        ->call('save');

    if ($expectedResultType === 'invalid') {
        $response->assertHasErrors(["state.$field" => $errorMessage]);
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
]);
