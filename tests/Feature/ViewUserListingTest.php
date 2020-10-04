<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewUserListingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_can_not_view_user_listings_page()
    {
        $this->get(route('users.index'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function an_authorized_user_can_view_user_listings_page()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('users.index'))
            ->assertSuccessful();
    }

    /** @test */
    public function guests_can_not_view_user_api_listings()
    {
        $this->get(route('users.listing'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function an_authorized_user_can_view_user_api_listings()
    {
        $user = User::factory()->create([
            'name'        => 'John Doe',
            'email'     => 'john@example.com',
        ]);

        $response = $this->actingAs($user)
            ->get(route('users.listing'))
            ->assertSuccessful();

        tap($response->json(), function ($users) {
            $this->assertCount(1, $users);
            $this->assertEquals('John Doe', $users[0]['name']);
            $this->assertEquals('john@example.com', $users[0]['email']);
        });
    }

    /** @test */
    public function the_user_listings_are_in_alphabetical_order()
    {
        $userA = User::factory()->create(['name' => 'John Doe']);
        $userB = User::factory()->create(['name' => 'Amy Smith']);
        $userC = User::factory()->create(['name' => 'Zach Williams']);

        $response = $this->actingAs($userA)
            ->get(route('users.listing'))
            ->assertSuccessful();

        $response->jsonData()->assertEquals([
            $userB,
            $userA,
            $userC
        ]);
    }
}
