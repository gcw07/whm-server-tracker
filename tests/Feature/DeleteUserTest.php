<?php

use App\Http\Livewire\User\Delete as UserDelete;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
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
        ->assertEmitted('closeModal')
        ->assertNoRedirect();

    $this->assertEquals(1, User::count());
});
