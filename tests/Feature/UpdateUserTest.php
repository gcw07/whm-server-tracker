<?php

use App\Http\Livewire\User\Edit as UserEdit;
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

test('an authorized user can edit a user', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(UserEdit::class, ['user' => $this->user])
        ->set('state', [
            'name' => 'Della Duck',
            'email' => 'della@example.com',
        ])
        ->call('save')
        ->assertRedirect(route('users.index'));

    tap($this->user->fresh(), function (User $user) {
        $this->assertEquals('Della Duck', $user->name);
        $this->assertEquals('della@example.com', $user->email);
    });
});

test('email must be unique for user edit', function () {
    $this->actingAs(User::factory()->create(['email' => 'grant@example.com']));

    $response = Livewire::test(UserEdit::class, ['user' => $this->user])
        ->set('state', [
            'name' => 'Della Duck',
            'email' => 'grant@example.com',
        ])
        ->call('save');

    $response->assertHasErrors(['state.email' => 'unique']);
});

test('email can be the same for the same user for user edit', function () {
    $user = User::factory()->create(['email' => 'grant@example.com']);
    $userB = User::factory()->create(['email' => 'mike@example.com']);

    $this->actingAs($user);

    Livewire::test(UserEdit::class, ['user' => $userB])
        ->set('state', [
            'name' => 'Mike Smith',
            'email' => 'mike@example.com',
        ])
        ->call('save')
        ->assertRedirect(route('users.index'));

    tap($userB->fresh(), function (User $user) {
        $this->assertEquals('mike@example.com', $user->email);
    });
});

it('validate rules for user edit', function ($data) {
    // This could be fixed in future pest version.
    $field = $data[0];
    $value = $data[1];
    $errorMessage = $data[2];

    $this->actingAs(User::factory()->create());

    $response = Livewire::test(UserEdit::class, ['user' => $this->user])
        ->set('state', $this->requestData->create([$field => $value]))
        ->call('save');

    $response->assertHasErrors(["state.$field" => $errorMessage]);
})->with([
    fn () => ['name', '', 'required'],
    fn () => ['email', '', 'required'],
    fn () => ['email', 'not-valid-email', 'email'],
]);
