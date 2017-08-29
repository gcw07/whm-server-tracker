<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateUserTest extends TestCase
{
    use RefreshDatabase;

    private function validParams($overrides = [])
    {
        return array_merge([
            'name'             => 'Grant Williams',
            'email'            => 'grant@example.com',
            'password'         => 'secret',
            'confirm_password' => 'secret'
        ], $overrides);
    }

    /** @test */
    public function guests_cannot_add_new_users()
    {
        $response = $this->postJson('/users', $this->validParams());

        $response->assertStatus(401);
        $this->assertEquals(0, User::count());
    }

    /** @test */
    public function an_authorized_user_can_add_a_valid_user()
    {
        $this->signIn();

        $response = $this->postJson('/users', $this->validParams([
            'name'                  => 'Grant Williams',
            'email'                 => 'grant@example.com',
            'password'              => 'secret',
            'password_confirmation' => 'secret'
        ]));

        $response->assertJson(['name' => 'Grant Williams']);
        $response->assertJson(['email' => 'grant@example.com']);
    }

    /** @test */
    public function name_is_required()
    {
        $this->signIn();

        $response = $this->postJson('/users', $this->validParams([
            'name' => '',
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('name');
        $this->assertEquals(1, User::count());
    }

    /** @test */
    public function email_is_required()
    {
        $this->signIn();

        $response = $this->postJson('/users', $this->validParams([
            'email' => '',
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('email');
        $this->assertEquals(1, User::count());
    }

    /** @test */
    public function email_must_be_a_valid_email()
    {
        $this->signIn();

        $response = $this->postJson('/users', $this->validParams([
            'email' => 'not-a-valid-email.com',
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('email');
        $this->assertEquals(1, User::count());
    }

    /** @test */
    public function email_must_be_unique()
    {
        $this->signIn();

        $userA = create('App\User', ['email' => 'grant@example.com']);

        $response = $this->postJson('/users', $this->validParams([
            'email' => 'grant@example.com',
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('email');
        $this->assertEquals(2, User::count());
    }

    /** @test */
    public function password_is_required()
    {
        $this->signIn();

        $response = $this->postJson('/users', $this->validParams([
            'password' => '',
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('password');
        $this->assertEquals(1, User::count());
    }

    /** @test */
    public function password_confirmation_is_required()
    {
        $this->signIn();

        $response = $this->postJson('/users', $this->validParams([
            'password_confirmation' => '',
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('password');
        $this->assertEquals(1, User::count());
    }

    /** @test */
    public function password_must_be_at_least_6_characters()
    {
        $this->signIn();

        $response = $this->postJson('/users', $this->validParams([
            'password'              => 'four',
            'password_confirmation' => 'four',
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('password');
        $this->assertEquals(1, User::count());
    }
}
