<?php

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\Factories\UserRequestDataFactory;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->requestData = UserRequestDataFactory::new();
});

test('guests cannot view the_add_user_form', function () {
    $this->get(route('users.create'))
        ->assertRedirect(route('login'));
});

test('an authorized user can view the add user form', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('users.create'))
        ->assertSuccessful();
});

test('guests cannot add new users', function () {
    $this->postJson(route('users.store'), $this->requestData->create())
        ->assertUnauthorized();

    $this->assertEquals(0, User::count());
});

test('an authorized user can add a valid user', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson(route('users.store'), $this->requestData->create([
            'name' => 'Grant Williams',
            'email' => 'grant@example.com',
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ]));

    $response->assertRedirect(route('users.index'));

    $this->assertDatabaseHas('users', [
        'name' => 'Grant Williams',
        'email' => 'grant@example.com',
    ]);
});

test('email must be unique for user create', function () {
    $user = User::factory()->create(['email' => 'grant@example.com']);

    $response = $this->actingAs($user)
        ->postJson(route('users.store'), $this->requestData->create([
            'email' => 'grant@example.com',
        ]));

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['email' => 'has already been taken']);
    $this->assertEquals(1, User::count());
});

test('password confirmation is required for user create', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson(route('users.store'), $this->requestData->create([
            'password_confirmation' => '',
        ]));

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['password' => 'confirmation does not match']);
    $this->assertEquals(1, User::count());
});

it('validates rules for create user form', function ($data) {
    // This could be fixed in future pest version.
    $field = $data[0];
    $value = $data[1];
    $errorMessage = $data[2];

    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson(route('users.store'), $this->requestData->create([
            $field => $value,
        ]));

    $response->assertStatus(422);
    $response->assertJsonValidationErrors([$field => $errorMessage]);
    $this->assertEquals(1, User::count());
})->with([
    fn () => ['name', '', 'field is required'],
    fn () => ['email', '', 'field is required'],
    fn () => ['email', 'not-valid-email', 'must be a valid email address'],
    fn () => ['password', '', 'field is required'],
    fn () => ['password', Str::random(5), 'must be at least 6 characters'],
]);
