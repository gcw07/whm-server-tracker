<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateUserTest extends TestCase
{
    use RefreshDatabase;

    private function oldAttributes($overrides = [])
    {
        return array_merge([
            'name'  => 'Old Name',
            'email' => 'old@example.com',
        ], $overrides);
    }

    private function validParams($overrides = [])
    {
        return array_merge([
            'name'  => 'New Name',
            'email' => 'new@example.com',
        ], $overrides);
    }

    /** @test */
    public function guests_cannot_view_the_edit_user_form()
    {
        $user = create('App\User');

        $response = $this->get("/users/{$user->id}/edit");

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function an_authorized_user_can_view_the_edit_user_form()
    {
        $this->signIn();
        $user = create('App\User');

        $response = $this->get("/users/{$user->id}/edit");

        $response->assertStatus(200);
        $this->assertTrue($response->data('user')->is($user));
    }

    /** @test */
    public function guests_cannot_edit_a_user()
    {
        $user = create('App\User', $this->oldAttributes());

        $response = $this->putJson("/users/{$user->id}", $this->validParams());

        $response->assertStatus(401);
        $this->assertArraySubset($this->oldAttributes(), $user->fresh()->getAttributes());
    }

    /** @test */
    function an_authorized_user_can_edit_a_user()
    {
        $this->signIn();

        $user = create('App\User', [
            'name'  => 'John Doe',
            'email' => 'john@example.com'
        ]);

        $response = $this->putJson("/users/{$user->id}", $this->validParams([
            'name'  => 'Jane Doe',
            'email' => 'jane@example.com'
        ]));

        tap($user->fresh(), function ($user) {
            $this->assertEquals('Jane Doe', $user->name);
            $this->assertEquals('jane@example.com', $user->email);
        });
    }

    /** @test */
    public function name_is_required()
    {
        $this->signIn();

        $user = create('App\User', [
            'name' => 'John Doe',
        ]);

        $response = $this->putJson("/users/{$user->id}", $this->validParams([
            'name' => '',
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('name');

        tap($user->fresh(), function ($user) {
            $this->assertEquals('John Doe', $user->name);
        });
    }

    /** @test */
    public function email_is_required()
    {
        $this->signIn();

        $user = create('App\User', [
            'email' => 'john@example.com',
        ]);

        $response = $this->putJson("/users/{$user->id}", $this->validParams([
            'email' => '',
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('email');

        tap($user->fresh(), function ($user) {
            $this->assertEquals('john@example.com', $user->email);
        });
    }

    /** @test */
    public function email_must_be_a_valid_email()
    {
        $this->signIn();

        $user = create('App\User', [
            'email' => 'john@example.com',
        ]);

        $response = $this->putJson("/users/{$user->id}", $this->validParams([
            'email' => 'johnexample.com',
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('email');

        tap($user->fresh(), function ($user) {
            $this->assertEquals('john@example.com', $user->email);
        });
    }

    /** @test */
    public function email_must_be_unique()
    {
        $this->signIn();

        $userA = create('App\User', ['email' => 'grant@example.com']);
        $userB = create('App\User', ['email' => 'john@example.com']);

        $response = $this->putJson("/users/{$userA->id}", $this->validParams([
            'email' => 'john@example.com',
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('email');

        tap($userA->fresh(), function ($user) {
            $this->assertEquals('grant@example.com', $user->email);
        });
    }

    /** @test */
    public function email_can_be_the_same_for_the_same_user()
    {
        $this->signIn();

        $userA = create('App\User', ['email' => 'grant@example.com']);

        $response = $this->putJson("/users/{$userA->id}", $this->validParams([
            'email' => 'grant@example.com',
        ]));

        $response->assertStatus(200);

        tap($userA->fresh(), function ($user) {
            $this->assertEquals('grant@example.com', $user->email);
        });
    }
}
