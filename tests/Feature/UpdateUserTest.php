<?php

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\Factories\UserRequestDataFactory;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->requestData = UserRequestDataFactory::new();
});

test('guests cannot view the edit user form', function () {
    $this->get(route('users.edit', $this->user->id))
        ->assertRedirect(route('login'));
});

test('an authorized user can view the edit user form', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('users.edit', $this->user->id))
        ->assertSuccessful();
});

test('guests cannot edit a user', function () {
    $user = User::factory()->create(['name' => 'Grant Williams']);

    $this->putJson(route('users.update', $user->id), $this->requestData->create())
        ->assertUnauthorized();

    tap($user->fresh(), function (User $user) {
        $this->assertEquals('Grant Williams', $user->name);
    });
});

test('an authorized user can edit a user', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->putJson(route('users.update', $this->user->id), $this->requestData->create([
            'name' => 'Della Duck',
            'email' => 'della@example.com',
        ]));

    $response->assertRedirect(route('users.index'));

    tap($this->user->fresh(), function (User $user) {
        $this->assertEquals('Della Duck', $user->name);
        $this->assertEquals('della@example.com', $user->email);
    });
});

test('email must be unique for user edit', function () {
    $user = User::factory()->create(['email' => 'grant@example.com']);

    $response = $this->actingAs($user)
        ->putJson(route('users.update', $this->user->id), $this->requestData->create([
            'email' => 'grant@example.com',
        ]));

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['email' => 'has already been taken']);
});

test('email can be the same for the same user for user edit', function () {
    $user = User::factory()->create(['email' => 'grant@example.com']);
    $userB = User::factory()->create(['email' => 'mike@example.com']);

    $response = $this->actingAs($user)
        ->putJson(route('users.update', $userB->id), $this->requestData->create([
            'email' => 'mike@example.com',
        ]));

    $response->assertRedirect(route('users.index'));

    tap($userB->fresh(), function (User $user) {
        $this->assertEquals('mike@example.com', $user->email);
    });
});

it('validate rules for user edit', function ($data) {
    // This could be fixed in future pest version.
    $field = $data[0];
    $value = $data[1];
    $errorMessage = $data[2];

    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->putJson(route('users.update', $this->user->id), $this->requestData->create([
            $field => $value,
        ]));

    $response->assertStatus(422);
    $response->assertJsonValidationErrors([$field => $errorMessage]);
})->with([
    fn () => ['name', '', 'field is required'],
    fn () => ['email', '', 'field is required'],
    fn () => ['email', 'not-valid-email', 'must be a valid email address'],
]);
