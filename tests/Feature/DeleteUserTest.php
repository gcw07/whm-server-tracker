<?php

use App\Livewire\User\Delete as UserDelete;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('guests cannot access the delete user component', function () {
    $response = Livewire::test(UserDelete::class, ['user' => $this->user]);
    $response->assertStatus(401);
});

test('an authorized user can delete a user', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(UserDelete::class, ['user' => $this->user])
        ->call('delete')
        ->assertRedirect(route('users.index'));

    $this->assertEquals(1, User::count());
});

test('an authorized user cannot delete themselves', function () {
    $this->actingAs($this->user);

    Livewire::test(UserDelete::class, ['user' => $this->user])
        ->call('delete')
        ->assertDispatched('closeModal')
        ->assertNoRedirect();

    $this->assertEquals(1, User::count());
});
