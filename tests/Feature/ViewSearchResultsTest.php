<?php

namespace Tests\Feature;

use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\Assert;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewSearchResultsTest extends TestCase
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

        Collection::macro('assertContains', function ($value) {
            Assert::assertTrue($this->contains($value), "Failed asserting that the collection contains the specified value.");
        });

        Collection::macro('assertNotContains', function ($value) {
            Assert::assertFalse($this->contains($value), "Failed asserting that the collection does not contain the specified value.");
        });
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

        $response->data('servers')->assertContains($server);
        $response->data('servers')->assertNotContains($serverB);
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

        $response->data('servers')->assertContains($server);
        $response->data('servers')->assertNotContains($serverB);
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

        $response->data('accounts')->assertContains($account);
        $response->data('accounts')->assertNotContains($accountB);
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

        $response->data('accounts')->assertContains($account);
        $response->data('accounts')->assertNotContains($accountB);
    }

    /** @test */
    public function the_account_username_can_be_searched()
    {
        $this->signIn();

        $account = create('App\Account', [
            'domain' => 'my-site.com',
            'user'     => 'mysite',
        ]);

        $accountB = create('App\Account', [
            'domain' => 'never-see.com',
            'user'     => 'neversee',
        ]);

        $response = $this->get("/search?q=mysite");

        $response->assertStatus(200);

        $response->data('accounts')->assertContains($account);
        $response->data('accounts')->assertNotContains($accountB);
    }
}
