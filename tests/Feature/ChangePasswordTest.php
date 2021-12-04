<?php

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('guests cannot change a password', function () {
    $this->putJson(route('users.change-password', $this->user->id), [
        'password' => 'secret',
        'password_confirmation' => 'secret',
    ])->assertUnauthorized();
});

test('an authorized user can change a password', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->putJson(route('users.change-password', $this->user->id), [
            'password' => 'secret',
            'password_confirmation' => 'secret'
        ])->assertSuccessful();
});

test('password confirmation is required for user change password', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->putJson(route('users.change-password', $this->user->id), [
            'password' => 'secret',
            'password_confirmation' => '',
        ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['password' => 'confirmation does not match']);
});

it('validates rules for change user password form', function ($data) {
    // This could be fixed in future pest version.
    $field = $data[0];
    $value = $data[1];
    $errorMessage = $data[2];

    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->putJson(route('users.change-password', $this->user->id), [
            $field => $value,
        ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors([$field => $errorMessage]);
})->with([
    fn() => ['password', '', 'field is required'],
    fn() => ['password', Str::random(5), 'must be at least 6 characters'],
]);
