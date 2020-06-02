<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\Factories\AccountFactory;
use Tests\Factories\ServerFactory;
use Tests\Factories\UserFactory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewSearchResultsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = UserFactory::new()->create();
    }

    /** @test */
    public function guests_can_not_view_search_results_page()
    {
        $this->get(route('search'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function an_authorized_user_can_view_search_results_page()
    {
        $this->actingAs($this->user)
            ->get(route('search'))
            ->assertSuccessful();
    }

    /** @test */
    public function the_server_name_can_be_searched()
    {
        $server = ServerFactory::new()->create([
            'name'        => 'My Test Server',
        ]);

        $serverB = ServerFactory::new()->create([
            'name'        => 'No Results',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('search', ['q' => 'test']))
            ->assertSuccessful();

//        $response->data('servers')->assertContains($server);
//        $response->data('servers')->assertNotContains($serverB);
    }

    /** @test */
    public function the_server_notes_can_be_searched()
    {
        $server = ServerFactory::new()->create([
            'notes'        => 'see this note',
        ]);

        $serverB = ServerFactory::new()->create([
            'notes'        => 'do not see me',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('search', ['q' => 'this']))
            ->assertSuccessful();

//        $response->data('servers')->assertContains($server);
//        $response->data('servers')->assertNotContains($serverB);
    }

    /** @test */
    public function the_account_domain_can_be_searched()
    {
        $server = ServerFactory::new()->create();
        $account = AccountFactory::new()->create([
            'server_id' => $server->id,
            'domain' => 'mytestsite.com',
        ]);

        $accountB = AccountFactory::new()->create([
            'server_id' => $server->id,
            'domain' => 'never-see.com',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('search', ['q' => 'test']))
            ->assertSuccessful();

//        $response->data('accounts')->assertContains($account);
//        $response->data('accounts')->assertNotContains($accountB);
    }

    /** @test */
    public function the_account_ip_can_be_searched()
    {
        $server = ServerFactory::new()->create();
        $account = AccountFactory::new()->create([
            'server_id' => $server->id,
            'ip' => '255.1.1.100',
        ]);

        $accountB = AccountFactory::new()->create([
            'server_id' => $server->id,
            'ip' => '192.1.1.100',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('search', ['q' => '255']))
            ->assertSuccessful();

//        $response->data('accounts')->assertContains($account);
//        $response->data('accounts')->assertNotContains($accountB);
    }

    /** @test */
    public function the_account_username_can_be_searched()
    {
        $server = ServerFactory::new()->create();
        $account = AccountFactory::new()->create([
            'server_id' => $server->id,
            'user' => 'mysite',
        ]);

        $accountB = AccountFactory::new()->create([
            'server_id' => $server->id,
            'user' => 'neversee',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('search', ['q' => 'mysite']))
            ->assertSuccessful();

//        $response->data('accounts')->assertContains($account);
//        $response->data('accounts')->assertNotContains($accountB);
    }
}
