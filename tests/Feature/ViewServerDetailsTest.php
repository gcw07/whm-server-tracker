<?php

use App\Http\Livewire\Server\Details as ServerDetails;
use App\Models\Server;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('guests can not view server details page', function () {
    $server = Server::factory()->create();

    $this->get(route('servers.show', $server->id))
        ->assertRedirect(route('login'));
});

test('an authorized user can view server details page', function () {
    $server = Server::factory()->create(['name' => 'MyServer.com']);

    $this->actingAs(User::factory()->create());

    Livewire::test(ServerDetails::class, ['server' => $server])
        ->assertSee('MyServer.com');
});
