<?php

use App\Models\Server;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('guests cannot edit a server token', function () {
    $server = Server::factory()->create(['token' => 'old-valid-api-token']);

    $this->putJson(route('servers.token', $server->id), [
        'token' => 'new-valid-api-token',
    ])->assertUnauthorized();

    $this->assertEquals('old-valid-api-token', $server->fresh()->token);
});

test('an authorized user can edit a server token', function () {
    $server = Server::factory()->create(['token' => 'old-server-api-token']);

    $this->actingAs($this->user)
        ->putJson(route('servers.token', $server->id), [
            'token' => 'new-server-api-token',
        ])->assertSuccessful();

    tap($server->fresh(), function (Server $server) {
        $this->assertEquals('new-server-api-token', $server->token);
    });
});

test('server token is required', function () {
    $server = Server::factory()->create(['token' => 'old-server-api-token']);

    $response = $this->actingAs($this->user)
        ->putJson(route('servers.token', $server->id), [
            'token' => '',
        ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['token' => 'field is required']);
});
