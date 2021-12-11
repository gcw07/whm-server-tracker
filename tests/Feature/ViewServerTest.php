<?php

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
    $user = User::factory()->create();
    $server = Server::factory()->create();

    $response = $this->actingAs($user)
        ->get(route('servers.show', $server->id))
        ->assertSuccessful();

//        $this->assertTrue($response->data('server')->is($server));
});
