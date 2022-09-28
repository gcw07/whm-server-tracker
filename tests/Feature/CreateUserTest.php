<?php

use App\Http\Livewire\User\Create as UserCreate;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\Factories\UserRequestDataFactory;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->requestData = UserRequestDataFactory::new();
});

test('guests cannot view the add user form', function () {
    $this->get(route('users.create'))
        ->assertRedirect(route('login'));
});

test('an authorized user can view the add user form', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('users.create'))
        ->assertSuccessful();
});

test('an authorized user can add a valid user', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(UserCreate::class)
        ->set('state', [
            'name' => 'Grant Williams',
            'email' => 'grant@example.com',
            'password' => 'NMeHq?Bzr#Nd#bt4',
            'password_confirmation' => 'NMeHq?Bzr#Nd#bt4',
            'notification_types' => [
                'uptime_check_failed' => false,
                'uptime_check_succeeded' => false,
                'uptime_check_recovered' => false,
                'certificate_check_succeeded' => false,
                'certificate_check_failed' => false,
                'certificate_expires_soon' => false,
                'fetched_server_data_succeeded' => false,
                'fetched_server_data_failed' => false,
            ],
        ])
        ->call('save')
        ->assertRedirect(route('users.index'));

    $this->assertDatabaseHas('users', [
        'name' => 'Grant Williams',
        'email' => 'grant@example.com',
    ]);
});

test('email must be unique for user create', function () {
    $user = User::factory()->create(['email' => 'grant@example.com']);

    $this->actingAs($user);

    $response = Livewire::test(UserCreate::class)
        ->set('state', $this->requestData->create([
            'email' => 'grant@example.com',
        ]))
        ->call('save');

    $response->assertHasErrors(['state.email' => 'unique']);
    $this->assertEquals(1, User::count());
});

test('password confirmation is required for user create', function () {
    $user = User::factory()->create(['email' => 'grant@example.com']);

    $this->actingAs($user);

    $response = Livewire::test(UserCreate::class)
        ->set('state', $this->requestData->create([
            'password_confirmation' => '',
        ]))
        ->call('save');

    $response->assertHasErrors(['state.password' => 'confirmed']);
    $this->assertEquals(1, User::count());
});

it('validates rules for create user form', function ($data) {
    // This could be fixed in future pest version.
    $field = $data[0];
    $value = $data[1];
    $errorMessage = $data[2];
    $subField = $data[3] ?? false;

    $this->actingAs(User::factory()->create());

    if ($subField) {
        $response = Livewire::test(UserCreate::class)
            ->set('state', $this->requestData->create([$field => [$subField => $value]]))
            ->call('save');

        $response->assertHasErrors(["state.$field.$subField" => $errorMessage]);
    } else {
        $response = Livewire::test(UserCreate::class)
            ->set('state', $this->requestData->create([$field => $value]))
            ->call('save');

        $response->assertHasErrors(["state.$field" => $errorMessage]);
    }

    $this->assertEquals(1, User::count());
})->with([
    fn () => ['name', '', 'required'],
    fn () => ['email', '', 'required'],
    fn () => ['email', 'not-valid-email', 'email'],
    fn () => ['password', '', 'required'],
    fn () => ['password', Str::random(5), 'Illuminate\Validation\Rules\Password'],
    fn () => ['notification_types', [], 'required'],
    fn () => ['notification_types', 'not-an-array', 'array'],
    fn () => ['notification_types', 'not-a-boolean', 'boolean', 'uptime_check_failed'],
    fn () => ['notification_types', 'not-a-boolean', 'boolean', 'uptime_check_succeeded'],
    fn () => ['notification_types', 'not-a-boolean', 'boolean', 'uptime_check_recovered'],
    fn () => ['notification_types', 'not-a-boolean', 'boolean', 'certificate_check_succeeded'],
    fn () => ['notification_types', 'not-a-boolean', 'boolean', 'certificate_check_failed'],
    fn () => ['notification_types', 'not-a-boolean', 'boolean', 'certificate_expires_soon'],
    fn () => ['notification_types', 'not-a-boolean', 'boolean', 'fetched_server_data_succeeded'],
    fn () => ['notification_types', 'not-a-boolean', 'boolean', 'fetched_server_data_failed'],
]);
