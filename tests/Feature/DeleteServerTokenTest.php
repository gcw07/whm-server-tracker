<?php

use App\Models\Server;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->server = Server::factory()->create([
        'token' => 'valid-api-token',
    ]);
});

test('guests cannot delete a server token', function () {
    $this->deleteJson(route('servers.token-destroy', $this->server->id))
        ->assertUnauthorized();

    $this->assertEquals('valid-api-token', $this->server->fresh()->token);
});

test('an authorized user can delete a server token', function () {
    $this->actingAs($this->user)
        ->deleteJson((route('servers.token-destroy', $this->server->id)))
        ->assertSuccessful();

    tap($this->server->fresh(), function (Server $server) {
        $this->assertNull($server->token);
    });
});
