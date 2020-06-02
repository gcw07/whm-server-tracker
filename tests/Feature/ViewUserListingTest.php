<?php

namespace Tests\Feature;

use Tests\Factories\UserFactory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
        $user = UserFactory::new()->create();

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
        $user = UserFactory::new()->create([
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
        $userA = UserFactory::new()->create(['name' => 'John Doe']);
        $userB = UserFactory::new()->create(['name' => 'Amy Smith']);
        $userC = UserFactory::new()->create(['name' => 'Zach Williams']);

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
