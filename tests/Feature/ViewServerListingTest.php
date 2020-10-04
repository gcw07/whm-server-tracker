<?php

namespace Tests\Feature;

use App\Enums\ServerTypeEnum;
use App\Models\Server;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewServerListingTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /** @test */
    public function guests_can_not_view_server_listings_page()
    {
        $this->get(route('servers.index'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function an_authorized_user_can_view_server_listings_page()
    {
        $this->actingAs($this->user)
            ->get(route('servers.index'))
            ->assertSuccessful();
    }

    /** @test */
    public function guests_can_not_view_server_api_listings()
    {
        $this->get(route('servers.listing'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function an_authorized_user_can_view_server_api_listings()
    {
        Server::factory()->create([
            'name'        => 'My Test Server',
            'address'     => '255.1.1.100',
            'port'        => 1111,
            'server_type' => 'dedicated',
            'notes'       => 'some server note',
            'token'       => 'new-server-api-token',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('servers.listing'))
            ->assertSuccessful();

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
        $serverA = Server::factory()->create(['name' => 'Some Server']);
        $serverB = Server::factory()->create(['name' => 'Another Server']);
        $serverC = Server::factory()->create(['name' => 'The Last Server']);

        $response = $this->actingAs($this->user)
            ->get(route('servers.listing'))
            ->assertSuccessful();

        $response->jsonData()->assertEquals([
            $serverB,
            $serverA,
            $serverC
        ]);
    }

    /** @test */
    public function the_server_listings_can_be_filtered_by_server_type()
    {
        $serverA = Server::factory()->create(['server_type' => ServerTypeEnum::vps()]);
        $serverB = Server::factory()->create(['server_type' => ServerTypeEnum::dedicated()]);

        $response = $this->actingAs($this->user)
            ->get(route('servers.listing', ['type' => 'vps']))
            ->assertSuccessful();

        tap($response->json(), function ($servers) {
            $this->assertCount(1, $servers);
            $this->assertEquals('vps', $servers[0]['server_type']);
        });
    }
}
