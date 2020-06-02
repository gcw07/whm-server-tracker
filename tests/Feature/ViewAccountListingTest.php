<?php

namespace Tests\Feature;

use App\Enums\ServerTypeEnum;
use App\Models\User;
use Tests\Factories\AccountFactory;
use Tests\Factories\ServerFactory;
use Tests\Factories\UserFactory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewAccountListingTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = UserFactory::new()->create();
    }

    /** @test */
    public function guests_can_not_view_account_listings_page()
    {
        $this->get(route('accounts.index'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function an_authorized_user_can_view_account_listings_page()
    {
        $this->actingAs($this->user)
            ->get(route('accounts.index'))
            ->assertSuccessful();
    }

    /** @test */
    public function guests_can_not_view_account_api_listings()
    {
        $this->get(route('accounts.listing'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function an_authorized_user_can_view_account_api_listings()
    {
        $server = ServerFactory::new()->create();
        AccountFactory::new()->create([
            'server_id' => $server->id,
            'domain' => 'mytestsite.com',
            'ip'     => '255.1.1.100',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('accounts.listing'))
            ->assertSuccessful();

        tap($response->json(), function ($accounts) {
            $this->assertCount(1, $accounts);
            $this->assertEquals('mytestsite.com', $accounts[0]['domain']);
            $this->assertEquals('255.1.1.100', $accounts[0]['ip']);
        });
    }

    /** @test */
    public function the_account_listings_are_in_alphabetical_order()
    {
        $server = ServerFactory::new()->create();

        $accountA = AccountFactory::new()->create(['server_id' => $server->id, 'domain' => 'somesite.com']);
        $accountB = AccountFactory::new()->create(['server_id' => $server->id, 'domain' => 'anothersite.com']);
        $accountC = AccountFactory::new()->create(['server_id' => $server->id, 'domain' => 'thelastsite.com']);

        $response = $this->actingAs($this->user)
            ->get(route('accounts.listing'))
            ->assertSuccessful();

        $response->jsonData()->assertEquals([
            $accountB,
            $accountA,
            $accountC
        ]);
    }

    /** @test */
    public function the_account_listings_can_be_filtered_by_server()
    {
        $serverA = ServerFactory::new()->create(['server_type' => ServerTypeEnum::VPS()]);
        $serverB = ServerFactory::new()->create(['server_type' => ServerTypeEnum::DEDICATED()]);

        $accountA = AccountFactory::new()->create(['server_id' => $serverA->id, 'domain' => 'somedomain.com']);
        $accountA = AccountFactory::new()->create(['server_id' => $serverB->id, 'domain' => 'anotherdomain.com']);

        $response = $this->actingAs($this->user)
            ->get(route('accounts.server-listing', $serverA->id))
            ->assertSuccessful();

        tap($response->json(), function ($accounts) {
            $this->assertCount(1, $accounts);
            $this->assertEquals('somedomain.com', $accounts[0]['domain']);
        });
    }
}
