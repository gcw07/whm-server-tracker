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
}
