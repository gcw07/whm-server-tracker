<?php

use App\Enums\ServerTypeEnum;
use App\Models\Account;
use App\Models\Server;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('guests can not view dashboard page', function () {
    $this->get(route('dashboard'))
        ->assertRedirect(route('login'));
});

test('an authorized user can view dashboard page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertSuccessful();
});

test('guests can not view dashboard api stats', function () {
    $this->get(route('dashboard.stats'))
        ->assertRedirect(route('login'));
});

test('an authorized user can view dashboard api stats', function () {
    $user = User::factory()->create();
    Server::factory()
        ->has(Account::factory()->count(5))
        ->count(3)
        ->create();

    $response = $this->actingAs($user)
        ->get(route('dashboard.stats'))
        ->assertSuccessful();

    tap($response->json(), function ($data) {
        $this->assertEquals(1, $data['users']);
        $this->assertEquals(3, $data['servers']);
        $this->assertEquals(15, $data['accounts']);
    });
});

test('guests can not view dashboard api servers', function () {
    $this->get(route('dashboard.servers'))
        ->assertRedirect(route('login'));
});

test('an authorized user can view dashboard api servers', function () {
    $user = User::factory()->create();

    Server::factory()->count(2)->create(['server_type' => ServerTypeEnum::Dedicated]);
    Server::factory()->count(3)->create(['server_type' => ServerTypeEnum::Vps]);
    Server::factory()->create(['server_type' => ServerTypeEnum::Reseller]);

    $response = $this->actingAs($user)
        ->get(route('dashboard.servers'))
        ->assertSuccessful();

    tap($response->json(), function ($data) {
        $this->assertEquals(2, $data['dedicated']);
        $this->assertEquals(3, $data['vps']);
        $this->assertEquals(1, $data['reseller']);
    });
});

test('guests can not view dashboard api latest accounts', function () {
    $this->get(route('dashboard.latest-accounts'))
        ->assertRedirect(route('login'));
});

test('an authorized user can view dashboard api latest accounts', function () {
    $user = User::factory()->create();

    $server = Server::factory()->create();
    Account::factory()->count(10)->create(['server_id' => $server->id]);

    $response = $this->actingAs($user)
        ->get(route('dashboard.latest-accounts'))
        ->assertSuccessful();

    tap($response->json(), function ($latest) {
        $this->assertCount(5, $latest);
    });
});
