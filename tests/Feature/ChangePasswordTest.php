<?php

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('an authorized user can change a password', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test('pages::user.change-password', ['user' => $this->user])
        ->set('form', [
            'password' => 'NMeHq?Bzr#Nd#bt4',
            'password_confirmation' => 'NMeHq?Bzr#Nd#bt4',
        ])
        ->call('save')
        ->assertRedirectToRoute('users.index');
});

test('password confirmation is required for user change password', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test('pages::user.change-password', ['user' => $this->user])
        ->set('form', [
            'password' => 'NMeHq?Bzr#Nd#bt4',
            'password_confirmation' => '',
        ])
        ->call('save')
        ->assertHasErrors(['form.password' => 'confirmed']);
});

it('validates rules for change user password form', function ($data) {
    // This could be fixed in future pest version.
    $field = $data[0];
    $value = $data[1];
    $errorMessage = $data[2];

    $this->actingAs(User::factory()->create());

    $response = Livewire::test('pages::user.change-password', ['user' => $this->user])
        ->set('form', [$field => $value])
        ->call('save');

    $response->assertHasErrors(["form.$field" => $errorMessage]);
})->with([
    fn () => ['password', '', 'required'],
    fn () => ['password', Str::random(5), 'Illuminate\Validation\Rules\Password'],
]);
