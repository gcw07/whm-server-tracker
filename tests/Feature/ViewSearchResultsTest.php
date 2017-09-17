<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewSearchResultsTest extends TestCase
{
    use RefreshDatabase;

    private function createSearchData()
    {
        $server = create('App\Server', [
            'name'        => 'My Test Server',
            'address'     => '255.1.1.100',
            'port'        => 1111,
            'server_type' => 'dedicated',
            'notes'       => 'some server note',
            'token'       => 'new-server-api-token',
        ]);

    }

    /** @test */
    public function guests_can_not_view_search_results_page()
    {
        $server = create('App\Server');

        $response = $this->get("/search");

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function an_authorized_user_can_view_search_results_page()
    {
        $this->signIn();

        $server = create('App\Server');

        $response = $this->get("/search");

        $response->assertStatus(200);
    }

    /** @test */
    public function the_server_name_can_be_searched()
    {
        $this->signIn();

        $server = create('App\Server', [
            'name'        => 'My Test Server',
            'address'     => '255.1.1.100',
            'port'        => 1111,
            'notes'       => 'some server note',
        ]);

        $serverB = create('App\Server', [
            'name'        => 'No Results',
            'address'     => '2.1.1.100',
            'port'        => 2222,
            'notes'       => 'another note',
        ]);

        $response = $this->get("/search?q=Test");

        $response->assertStatus(200);

        $response->assertSee($server->name)
            ->assertDontSee($serverB->name);
    }

    /** @test */
    public function the_server_notes_can_be_searched()
    {
        $this->signIn();

        $server = create('App\Server', [
            'name'        => 'My Test Server',
            'address'     => '255.1.1.100',
            'port'        => 1111,
            'notes'       => 'see this note',
        ]);

        $serverB = create('App\Server', [
            'name'        => 'No Results',
            'address'     => '2.1.1.100',
            'port'        => 2222,
            'notes'       => 'do not see me',
        ]);

        $response = $this->get("/search?q=this");

        $response->assertStatus(200);

        $response->assertSee($server->name)
            ->assertDontSee($serverB->name);
    }

    /** @test */
    public function the_account_domain_can_be_searched()
    {
        $this->signIn();

        $account = create('App\Account', [
            'domain' => 'mytestsite.com',
            'ip'     => '255.1.1.100',
        ]);

        $accountB = create('App\Account', [
            'domain' => 'never-see.com',
            'ip'     => '255.1.1.100',
        ]);

        $response = $this->get("/search?q=Test");

        $response->assertStatus(200);

        $response->assertSee($account->domain)
            ->assertDontSee($accountB->domain);
    }

    /** @test */
    public function the_account_ip_can_be_searched()
    {
        $this->signIn();

        $account = create('App\Account', [
            'domain' => 'mytestsite.com',
            'ip'     => '255.1.1.100',
        ]);

        $accountB = create('App\Account', [
            'domain' => 'never-see.com',
            'ip'     => '192.1.1.100',
        ]);

        $response = $this->get("/search?q=255");

        $response->assertStatus(200);

        $response->assertSee($account->domain)
            ->assertDontSee($accountB->domain);
    }
}
