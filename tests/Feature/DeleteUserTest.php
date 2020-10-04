<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteUserTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /** @test */
    public function guests_cannot_delete_a_user()
    {
        $this->deleteJson(route('users.destroy', $this->user->id))
            ->assertUnauthorized();

        $this->assertEquals(1, User::count());
    }

    /** @test */
    public function an_authorized_user_can_delete_a_user()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->deleteJson((route('users.destroy', $this->user->id)))
            ->assertSuccessful();

        $this->assertEquals(1, User::count());
    }

    /** @test */
    public function an_authorized_user_cannot_delete_themselves()
    {
        $response = $this->actingAs($this->user)
            ->deleteJson((route('users.destroy', $this->user->id)));

        $response->assertStatus(422);

        $this->assertEquals(1, User::count());
    }
}
