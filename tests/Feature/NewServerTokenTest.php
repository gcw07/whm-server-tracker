<?php

use App\Livewire\Server\NewToken as ServerNewToken;
use App\Models\Server;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->server = Server::factory()->create(['token' => 'old-api-token']);
    $this->user = User::factory()->create();
});

test('guests cannot access the new server token component', function () {
    $response = Livewire::test(ServerNewToken::class, ['server' => $this->server]);
    $response->assertStatus(401);
});

test('an authorized user can add a new server token', function () {
    $this->actingAs(User::factory()->create());

    $server = Server::factory()->create(['token' => null]);

    Livewire::test(ServerNewToken::class, ['server' => $server])
        ->set('state', [
            'token' => 'new-api-token',
        ])
        ->call('save')
        ->assertRedirectToRoute('servers.show', $server->id);
});

test('an authorized user can update a server token', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(ServerNewToken::class, ['server' => $this->server])
        ->set('state', [
            'token' => 'new-api-token',
        ])
        ->call('save')
        ->assertDispatched('closeModal')
        ->assertNoRedirect();

    tap($this->server->fresh(), function (Server $server) {
        $this->assertEquals('new-api-token', $server->token);
    });
});

test('server token is required', function () {
    $this->actingAs(User::factory()->create());

    $response = Livewire::test(ServerNewToken::class, ['server' => $this->server])
        ->set('state', [
            'token' => '',
        ])
        ->call('save');

    $response->assertHasErrors(['state.token' => 'required']);
});
