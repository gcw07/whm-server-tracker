<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteUserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_cannot_delete_a_user()
    {
        $user = create('App\User', [
            'name' => 'John Doe'
        ]);

        $response = $this->deleteJson("/users/{$user->id}");

        $response->assertStatus(401);
        $this->assertEquals('John Doe', $user->fresh()->name);
    }

    /** @test */
    function an_authorized_user_can_delete_a_user()
    {
        $this->signIn();

        $user = create('App\User', [
            'name' => 'John Doe'
        ]);

        $response = $this->deleteJson("/users/{$user->id}");

        $response->assertStatus(204);
        $this->assertEquals(1, User::count());
    }

    /** @test */
    public function an_authorized_user_cannot_delete_themselves()
    {
        $user = create('App\User', [
            'name' => 'John Doe'
        ]);

        $this->signIn($user);

        $response = $this->deleteJson("/users/{$user->id}");

        $response->assertStatus(422);
        $this->assertEquals(1, User::count());
    }
}
