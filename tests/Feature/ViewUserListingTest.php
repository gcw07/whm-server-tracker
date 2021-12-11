<?php

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('guests can not view user listings page', function () {
    $this->get(route('users.index'))
        ->assertRedirect(route('login'));
});

//test('an authorized user can view user listings page', function () {
//    $user = User::factory()->create();
//
//    $this->actingAs($user)
//        ->get(route('users.index'))
//        ->assertSuccessful();
//});

test('guests can not view user api listings', function () {
    $this->get(route('users.listing'))
        ->assertRedirect(route('login'));
});

test('an authorized user can view user api listings', function () {
    $user = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    $response = $this->actingAs($user)
        ->get(route('users.listing'))
        ->assertSuccessful();

    tap($response->json(), function ($users) {
        $this->assertCount(1, $users);
        $this->assertEquals('John Doe', $users[0]['name']);
        $this->assertEquals('john@example.com', $users[0]['email']);
    });
});

test('the user listings are in alphabetical order', function () {
    $userA = User::factory()->create(['name' => 'John Doe']);
    $userB = User::factory()->create(['name' => 'Amy Smith']);
    $userC = User::factory()->create(['name' => 'Zach Williams']);

    $response = $this->actingAs($userA)
        ->get(route('users.listing'))
        ->assertSuccessful();

    $response->jsonData()->assertEquals([
        $userB,
        $userA,
        $userC
    ]);
});
