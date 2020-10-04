<?php

namespace Tests\Feature;

use App\Enums\ServerTypeEnum;
use App\Models\Account;
use App\Models\Server;
use App\Models\User;
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
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertSuccessful();
    }

    /** @test */
    public function guests_can_not_view_dashboard_api_stats()
    {
        $this->get(route('dashboard.stats'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function an_authorized_user_can_view_dashboard_api_stats()
    {
        $user = User::factory()->create();
        Server::factory()
            ->has(Account::factory()->count(5))
            ->count(3)
            ->create();

        $response = $this->actingAs($user)
            ->get(route('dashboard.stats'))
            ->assertSuccessful();

        tap($response->json(), function ($data) {
            $this->assertEquals(1, $data['users']);
            $this->assertEquals(3, $data['servers']);
            $this->assertEquals(15, $data['accounts']);
        });
    }

    /** @test */
    public function guests_can_not_view_dashboard_api_servers()
    {
        $this->get(route('dashboard.servers'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function an_authorized_user_can_view_dashboard_api_servers()
    {
        $user = User::factory()->create();

        Server::factory()->count(2)->create(['server_type' => ServerTypeEnum::dedicated()]);
        Server::factory()->count(3)->create(['server_type' => ServerTypeEnum::vps()]);
        Server::factory()->create(['server_type' => ServerTypeEnum::reseller()]);

        $response = $this->actingAs($user)
            ->get(route('dashboard.servers'))
            ->assertSuccessful();

        tap($response->json(), function ($data) {
            $this->assertEquals(2, $data['dedicated']);
            $this->assertEquals(3, $data['vps']);
            $this->assertEquals(1, $data['reseller']);
        });
    }

    /** @test */
    public function guests_can_not_view_dashboard_api_latest_accounts()
    {
        $this->get(route('dashboard.latest-accounts'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function an_authorized_user_can_view_dashboard_api_latest_accounts()
    {
        $user = User::factory()->create();

        $server = Server::factory()->create();
        Account::factory()->count(10)->create(['server_id' => $server->id]);

        $response = $this->actingAs($user)
            ->get(route('dashboard.latest-accounts'))
            ->assertSuccessful();

        tap($response->json(), function ($latest) {
            $this->assertCount(5, $latest);
        });
    }
}
