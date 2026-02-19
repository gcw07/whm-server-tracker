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
        ->assertRedirectToRoute('login');
});

test('an authorized user can view the add server form', function () {
    $this->actingAs($this->user)
        ->get(route('servers.create'))
        ->assertSuccessful();
});

test('an authorized user can add a valid server', function () {
    $this->actingAs($this->user);

    $response = Livewire::test('pages::server.create')
        ->set('form', [
            'name' => 'My Test Server',
            'address' => '255.1.1.100',
            'port' => 1111,
            'serverType' => ServerTypeEnum::Dedicated,
            'notes' => 'some server note',
            'token' => 'new-server-api-token',
        ])
        ->call('save');

    $server = Server::first();

    $response->assertRedirectToRoute('servers.show', $server->id);

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

    $this->actingAs($this->user);

    $response = Livewire::test('pages::server.create')
        ->set('form', $this->requestData->create([$field => $value]))
        ->call('save');

    if ($expectedResultType === 'invalid') {
        $response->assertHasErrors(["form.$field" => $errorMessage]);
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
    fn () => ['serverType', '', 'invalid', 'required'],
    fn () => ['serverType', 'not-valid-type', 'invalid', 'Illuminate\Validation\Rules\Enum'],
    fn () => ['notes', '', 'success', null],
    fn () => ['token', '', 'success', null],
]);
