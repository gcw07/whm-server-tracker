<?php

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('guests can not view user listings page', function () {
    $this->get(route('users.index'))
        ->assertRedirectToRoute('login');
});

test('an authorized user can view user listings page', function () {
    $this->actingAs(User::factory()->create([
        'name' => 'John Doe',
    ]));

    Livewire::test('pages::user.listings')
        ->assertSee('John Doe');
});

test('the user listings are in alphabetical order', function () {
    $userA = User::factory()->create(['name' => 'John Doe']);
    $userB = User::factory()->create(['name' => 'Amy Smith']);
    $userC = User::factory()->create(['name' => 'Zach Williams']);

    $this->actingAs($userA);

    Livewire::test('pages::user.listings')
        ->assertViewHas('users', function ($users) {
            return count($users) === 3;
        });
})->skip();
