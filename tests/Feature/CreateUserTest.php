<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Str;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateUserTest extends TestCase
{
    use RefreshDatabase;

    private function validParams($overrides = [])
    {
        return array_merge([
            'name'     => 'Grant Williams',
            'email'    => 'grant@example.com',
            'password' => 'secret',
        ], $overrides);
    }

    /** @test */
    public function guests_cannot_view_the_add_user_form()
    {
        $this->get(route('users.create'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function an_authorized_user_can_view_the_add_user_form()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('users.create'))
            ->assertSuccessful();
    }

    /** @test */
    public function guests_cannot_add_new_users()
    {
        $this->postJson(route('users.store'), $this->validParams())
            ->assertUnauthorized();

        $this->assertEquals(0, User::count());
    }

    /** @test */
    public function an_authorized_user_can_add_a_valid_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson(route('users.store'), $this->validParams([
                'name'                  => 'Grant Williams',
            'email'                 => 'grant@example.com',
            'password'              => 'secret',
            'password_confirmation' => 'secret'
            ]));

        $response->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'name' => 'Grant Williams',
            'email' => 'grant@example.com',
        ]);
    }

    /**
     * @dataProvider validationDataProvider
     * @test
     * @param $field
     * @param $value
     * @param $errorMessage
     */
    public function validate_rules_for_user_create($field, $value, $errorMessage)
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson(route('users.store'), $this->validParams([
                $field => $value,
            ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([$field => $errorMessage]);
        $this->assertEquals(1, User::count());
    }

    public function validationDataProvider()
    {
        return [
            'name is required' => ['name', '', 'field is required'],
            'email is required' => ['email', '', 'field is required'],
            'email must be valid' => ['email', 'not-valid-email', 'must be a valid email address'],
            'password is required' => ['password', '', 'field is required'],
            'password must be at least 6 characters' => ['password', Str::random(5), 'must be at least 6 characters'],
        ];
    }

    /** @test */
    public function email_must_be_unique_for_user_create()
    {
        $user = User::factory()->create(['email' => 'grant@example.com']);

        $response = $this->actingAs($user)
            ->postJson(route('users.store'), $this->validParams([
                'email' => 'grant@example.com',
            ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email' => 'has already been taken']);
        $this->assertEquals(1, User::count());
    }

    /** @test */
    public function password_confirmation_is_required_for_user_create()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson(route('users.store'), $this->validParams([
                'password_confirmation' => '',
            ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password' => 'confirmation does not match']);
        $this->assertEquals(1, User::count());
    }
}
