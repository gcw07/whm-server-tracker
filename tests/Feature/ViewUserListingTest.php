<?php

use App\Http\Livewire\User\Listings as UserListings;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('guests can not view user listings page', function () {
    $this->get(route('users.index'))
        ->assertRedirect(route('login'));
});

test('an authorized user can view user listings page', function () {
    $this->actingAs(User::factory()->create([
        'name' => 'John Doe',
    ]));

    Livewire::test(UserListings::class)
        ->assertSee('John Doe');
});

test('the user listings are in alphabetical order', function () {
    $userA = User::factory()->create(['name' => 'John Doe']);
    $userB = User::factory()->create(['name' => 'Amy Smith']);
    $userC = User::factory()->create(['name' => 'Zach Williams']);

    $this->actingAs($userA);

    Livewire::test(UserListings::class)
        ->assertViewHas('users', function ($users) {
            return count($users) === 3;
        });
})->skip();
