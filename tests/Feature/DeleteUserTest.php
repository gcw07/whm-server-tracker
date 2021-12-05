<?php

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('guests cannot delete a user', function () {
    $this->deleteJson(route('users.destroy', $this->user->id))
        ->assertUnauthorized();

    $this->assertEquals(1, User::count());
});

test('an authorized user can delete a user', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->deleteJson((route('users.destroy', $this->user->id)))
        ->assertSuccessful();

    $this->assertEquals(1, User::count());
});

test('an authorized user cannot delete themselves', function () {
    $response = $this->actingAs($this->user)
        ->deleteJson((route('users.destroy', $this->user->id)));

    $response->assertStatus(422);

    $this->assertEquals(1, User::count());
});
