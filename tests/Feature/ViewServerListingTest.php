<?php

use App\Enums\ServerTypeEnum;
use App\Models\Server;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('guests can not view server listings page', function () {
    $this->get(route('servers.index'))
        ->assertRedirect(route('login'));
});

test('an authorized user can view server listings page', function () {
    $this->actingAs($this->user)
        ->get(route('servers.index'))
        ->assertSuccessful();
});

test('guests can not view server api listings', function () {
    $this->get(route('servers.listing'))
        ->assertRedirect(route('login'));
});

test('an authorized user can view server api listings', function () {
    Server::factory()->create([
        'name' => 'My Test Server',
        'address' => '255.1.1.100',
        'port' => 1111,
        'server_type' => 'dedicated',
        'notes' => 'some server note',
        'token' => 'new-server-api-token',
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('servers.listing'))
        ->assertSuccessful();

    tap($response->json(), function ($servers) {
        $this->assertCount(1, $servers);
        $this->assertEquals('My Test Server', $servers[0]['name']);
        $this->assertEquals('255.1.1.100', $servers[0]['address']);
        $this->assertEquals('dedicated', $servers[0]['server_type']);
    });
});

test('the server listings are in alphabetical order', function () {
    $serverA = Server::factory()->create(['name' => 'Some Server']);
    $serverB = Server::factory()->create(['name' => 'Another Server']);
    $serverC = Server::factory()->create(['name' => 'The Last Server']);

    $response = $this->actingAs($this->user)
        ->get(route('servers.listing'))
        ->assertSuccessful();

    $response->jsonData()->assertEquals([
        $serverB,
        $serverA,
        $serverC,
    ]);
});

test('the server listings can be filtered by server type', function () {
    $serverA = Server::factory()->create(['server_type' => ServerTypeEnum::vps()]);
    $serverB = Server::factory()->create(['server_type' => ServerTypeEnum::dedicated()]);

    $response = $this->actingAs($this->user)
        ->get(route('servers.listing', ['type' => 'vps']))
        ->assertSuccessful();

    tap($response->json(), function ($servers) {
        $this->assertCount(1, $servers);
        $this->assertEquals('vps', $servers[0]['server_type']);
    });
});
