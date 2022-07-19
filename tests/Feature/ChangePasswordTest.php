<?php

use App\Http\Livewire\User\ChangePassword as UserChangePassword;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('guests cannot access the change password component', function () {
    $response = Livewire::test(UserChangePassword::class, ['user' => $this->user]);
    $response->assertStatus(401);
});

test('an authorized user can change a password', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(UserChangePassword::class, ['user' => $this->user])
        ->set('state', [
            'password' => 'NMeHq?Bzr#Nd#bt4',
            'password_confirmation' => 'NMeHq?Bzr#Nd#bt4',
        ])
        ->call('save')
        ->assertEmitted('closeModal');
});

test('password confirmation is required for user change password', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(UserChangePassword::class, ['user' => $this->user])
        ->set('state', [
            'password' => 'NMeHq?Bzr#Nd#bt4',
            'password_confirmation' => '',
        ])
        ->call('save')
        ->assertHasErrors(['state.password' => 'confirmed']);
});

it('validates rules for change user password form', function ($data) {
    // This could be fixed in future pest version.
    $field = $data[0];
    $value = $data[1];
    $errorMessage = $data[2];

    $this->actingAs(User::factory()->create());

    $response = Livewire::test(UserChangePassword::class, ['user' => $this->user])
        ->set('state', [$field => $value])
        ->call('save');

    $response->assertHasErrors(["state.$field" => $errorMessage]);
})->with([
    fn () => ['password', '', 'required'],
    fn () => ['password', Str::random(5), 'Illuminate\Validation\Rules\Password'],
]);
