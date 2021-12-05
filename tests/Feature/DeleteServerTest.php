<?php

use App\Models\Account;
use App\Models\Server;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->server = Server::factory()->create();
});

test('guests cannot delete a server', function () {
    $this->deleteJson(route('servers.destroy', $this->server->id))
        ->assertUnauthorized();

    $this->assertEquals(1, Server::count());
});

test('an authorized user can delete a server', function () {
    $this->actingAs($this->user)
        ->deleteJson((route('servers.destroy', $this->server->id)))
        ->assertSuccessful();

    $this->assertEquals(0, Server::count());
});

test('accounts are deleted when a server is deleted', function () {
    $server = Server::factory()
        ->has(Account::factory()->count(5))
        ->create(['name' => 'my-server-name']);

    Server::factory()
        ->has(Account::factory()->count(1))
        ->create(['name' => 'other-server-name']);

    $this->assertEquals(3, Server::count());

    tap($server->fresh(), function (Server $server) {
        $this->assertCount(5, $server->accounts);
    });

    $this->actingAs($this->user)
        ->deleteJson((route('servers.destroy', $server->id)))
        ->assertSuccessful();

    $this->assertEquals(2, Server::count());
    $this->assertEquals(1, Account::count());
});
