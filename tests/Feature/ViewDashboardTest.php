<?php

namespace Tests\Feature;

use Tests\Factories\UserFactory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewDashboardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_can_not_view_dashboard_page()
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function an_authorized_user_can_view_dashboard_page()
    {
        $user = UserFactory::new()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertSuccessful();
    }

    /** @test */
    public function guests_can_not_view_dashboard_api_stats()
    {
        $response = $this->get("/api/dashboard/stats");

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function an_authorized_user_can_view_dashboard_api_stats()
    {
        $user = create('App\User');
        $this->signIn($user);

        $servers = create('App\Server', [], 3);
        $servers->each(function ($item) {
            create('App\Account', ['server_id' => $item->id], 5);
        });

        $response = $this->get("/api/dashboard/stats");

        $response->assertStatus(200);

        tap($response->json(), function ($data) {
            $this->assertEquals(1, $data['users']);
            $this->assertEquals(3, $data['servers']);
            $this->assertEquals(15, $data['accounts']);
        });
    }

    /** @test */
    public function guests_can_not_view_dashboard_api_servers()
    {
        $response = $this->get("/api/dashboard/servers");

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function an_authorized_user_can_view_dashboard_api_servers()
    {
        $this->signIn();
        $serversA = create('App\Server', ['server_type' => 'dedicated'], 2);
        $serversB = create('App\Server', ['server_type' => 'vps'], 3);
        $serversC = create('App\Server', ['server_type' => 'reseller']);

        $response = $this->get("/api/dashboard/servers");

        $response->assertStatus(200);

        tap($response->json(), function ($data) {
            $this->assertEquals(2, $data['dedicated']);
            $this->assertEquals(3, $data['vps']);
            $this->assertEquals(1, $data['reseller']);
        });
    }

    /** @test */
    public function guests_can_not_view_dashboard_api_latest_accounts()
    {
        $response = $this->get("/api/dashboard/latest-accounts");

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function an_authorized_user_can_view_dashboard_api_latest_accounts()
    {
        $this->signIn();
        $accounts = create('App\Account', [], 10);

        $response = $this->get("/api/dashboard/latest-accounts");

        $response->assertStatus(200);

        tap($response->json(), function ($latest) {
            $this->assertCount(5, $latest);
        });
    }
}
