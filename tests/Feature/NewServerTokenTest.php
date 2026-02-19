<?php

use App\Models\Server;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->server = Server::factory()->create(['token' => 'old-api-token']);
    $this->user = User::factory()->create();
});

test('an authorized user can add a new server token', function () {
    $this->actingAs(User::factory()->create());

    $server = Server::factory()->create(['token' => null]);

    Livewire::test('pages::server.details', ['server' => $server])
        ->set('newToken', 'new-api-token')
        ->call('saveNewApiToken')
        ->assertRedirectToRoute('servers.show', $server->id);
});

test('an authorized user can update a server token', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test('pages::server.details', ['server' => $this->server])
        ->set('newToken', 'new-api-token')
        ->call('saveNewApiToken')
        ->assertNoRedirect();

    tap($this->server->fresh(), function (Server $server) {
        $this->assertEquals('new-api-token', $server->token);
    });
});

test('server token is required', function () {
    $this->actingAs(User::factory()->create());

    $response = Livewire::test('pages::server.details', ['server' => $this->server])
        ->set('newToken', '')
        ->call('saveNewApiToken');

    $response->assertHasErrors(['newToken' => 'required']);
});
