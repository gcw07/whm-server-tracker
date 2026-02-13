<?php

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('an authorized user can delete a user', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test('pages::user.listings')
        ->call('delete', $this->user->id)
        ->assertRedirectToRoute('users.index');

    $this->assertEquals(1, User::count());
});

test('an authorized user cannot delete themselves', function () {
    $this->actingAs($this->user);

    Livewire::test('pages::user.listings')
        ->call('delete', $this->user->id)
        ->assertNoRedirect();

    $this->assertEquals(1, User::count());
});
