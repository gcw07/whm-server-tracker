<?php

namespace Tests\Feature;

use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\Assert;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewServerListingTest extends TestCase
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
    public function guests_can_not_view_server_listings_page()
    {
        $server = create('App\Server');

        $response = $this->get("/servers");

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function an_authorized_user_can_view_server_listings_page()
    {
        $this->signIn();

        $server = create('App\Server');

        $response = $this->get("/servers");

        $response->assertStatus(200);
    }

    /** @test */
    public function guests_can_not_view_server_api_listings()
    {
        $server = create('App\Server');

        $response = $this->get("/api/servers");

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function an_authorized_user_can_view_server_api_listings()
    {
        $this->signIn();

        $server = create('App\Server', [
            'name'             => 'My Test Server',
            'address'          => '255.1.1.100',
            'port'             => 1111,
            'server_type'      => 'dedicated',
            'notes'            => 'some server note',
            'token'            => 'new-server-api-token',
            'disk_used'        => 10000000,
            'disk_available'   => 115000000,
            'disk_total'       => 125000000,
            'disk_percentage'  => 8,
            'backup_enabled'   => false,
            'backup_days'      => '0,1',
            'backup_retention' => 5
        ]);

        $response = $this->get("/api/servers");

        $response->assertStatus(200);

        tap($response->json(), function ($servers) {
            $this->assertCount(1, $servers);
            $this->assertEquals('My Test Server', $servers[0]['name']);
            $this->assertEquals('255.1.1.100', $servers[0]['address']);
            $this->assertEquals('dedicated', $servers[0]['server_type']);
        });
    }

    /** @test */
    public function the_server_listings_are_in_alphabetical_order()
    {
        $this->signIn();

        $serverA = create('App\Server', ['name' => 'Some Server']);
        $serverB = create('App\Server', ['name' => 'Another Server']);
        $serverC = create('App\Server', ['name' => 'The Last Server']);

        $response = $this->get("/api/servers");

        $response->assertStatus(200);

        $response->jsonData()->assertEquals([
            $serverB,
            $serverA,
            $serverC
        ]);
    }
}
