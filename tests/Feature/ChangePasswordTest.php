<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_cannot_change_a_password()
    {
        $user = create('App\User');

        $response = $this->putJson("/users/{$user->id}/change-password", [
            'password'              => 'secret',
            'password_confirmation' => 'secret',
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    function an_authorized_user_can_change_a_password()
    {
        $this->signIn();

        $user = create('App\User');

        $response = $this->putJson("/users/{$user->id}/change-password", [
            'password'              => 'secret',
            'password_confirmation' => 'secret',
        ]);

        $response->assertStatus(204);
    }

    /** @test */
    public function password_is_required()
    {
        $this->signIn();

        $user = create('App\User');

        $response = $this->putJson("/users/{$user->id}/change-password", [
            'password' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonHasErrors('password');
    }

    /** @test */
    public function password_confirmation_is_required()
    {
        $this->signIn();

        $user = create('App\User');

        $response = $this->putJson("/users/{$user->id}/change-password", [
            'password_confirmation' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonHasErrors('password');
    }

    /** @test */
    public function password_must_be_at_least_6_characters()
    {
        $this->signIn();

        $user = create('App\User');

        $response = $this->putJson("/users/{$user->id}/change-password", [
            'password'              => 'four',
            'password_confirmation' => 'four',
        ]);

        $response->assertStatus(422);
        $response->assertJsonHasErrors('password');
    }
}
