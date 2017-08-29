<?php

namespace Tests\Feature;

use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\Assert;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewUserListingTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        Collection::macro('assertEquals', function ($items) {
            Assert::assertEquals(count($this), count($items));

            $this->zip($items)->each(function ($pair) {
                list($a, $b) = $pair;
                Assert::assertTrue($a->is($b));
            });
        });
    }

    /** @test */
    public function guests_can_not_view_user_listings_page()
    {
        $user = create('App\User');

        $response = $this->get("/users");

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function an_authorized_user_can_view_user_listings_page()
    {
        $this->signIn();

        $user = create('App\User');

        $response = $this->get("/users");

        $response->assertStatus(200);
    }

    /** @test */
    public function guests_can_not_view_user_api_listings()
    {
        $user = create('App\User');

        $response = $this->get("/api/users");

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function an_authorized_user_can_view_user_api_listings()
    {
        $user = create('App\User', [
            'name'        => 'John Doe',
            'email'     => 'john@example.com',
        ]);

        $this->signIn($user);

        $response = $this->get("/api/users");

        $response->assertStatus(200);

        tap($response->json(), function ($users) {
            $this->assertCount(1, $users);
            $this->assertEquals('John Doe', $users[0]['name']);
            $this->assertEquals('john@example.com', $users[0]['email']);
        });
    }

    /** @test */
    public function the_user_listings_are_in_alphabetical_order()
    {
        $userA = create('App\User', ['name' => 'John Doe']);
        $userB = create('App\User', ['name' => 'Amy Smith']);
        $userC = create('App\User', ['name' => 'Zach Williams']);

        $this->signIn($userA);

        $response = $this->get("/api/users");

        $response->assertStatus(200);

        $response->jsonData()->assertEquals([
            $userB,
            $userA,
            $userC
        ]);
    }
}
