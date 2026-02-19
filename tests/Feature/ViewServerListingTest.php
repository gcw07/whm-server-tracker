<?php

use App\Enums\ServerTypeEnum;
use App\Models\Server;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('guests can not view server listings page', function () {
    $this->get(route('servers.index'))
        ->assertRedirectToRoute('login');
});

test('an authorized user can view server listings page', function () {
    Server::factory()->create(['name' => 'MyServer.com']);
    Server::factory()->count(4)->create();

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::server.listings')
        ->assertCount('servers', 5)
        ->assertSee('MyServer.com');
});

test('the server listings are in alphabetical order', function () {
    Server::factory()->count(3)->state(new Sequence(
        ['name' => 'SomeServer.com'],
        ['name' => 'AnotherServer.com'],
        ['name' => 'TheLastServer.com'],
    ))->create();

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::server.listings')
        ->assertViewHas('servers', function ($servers) {
            return count($servers) === 3;
        });
})->skip();

test('the server listings can be filtered by server type', function () {
    Server::factory()->count(3)->state(new Sequence(
        ['name' => 'SomeServer.com', 'server_type' => ServerTypeEnum::Vps],
        ['name' => 'AnotherServer.com', 'server_type' => ServerTypeEnum::Vps],
        ['name' => 'TheLastServer.com', 'server_type' => ServerTypeEnum::Dedicated],
    ))->create();

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::server.listings')
        ->set('serverType', 'vps')
        ->assertCount('servers', 2)
        ->assertSee('SomeServer.com');
});
