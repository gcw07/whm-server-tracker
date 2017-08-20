<?php

namespace Tests\Feature;

use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\Assert;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewAccountListingTest extends TestCase
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
    public function guests_can_not_view_account_listings_page()
    {
        $account = create('App\Account');

        $response = $this->get("/accounts");

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function an_authorized_user_can_view_account_listings_page()
    {
        $this->signIn();

        $account = create('App\Account');

        $response = $this->get("/accounts");

        $response->assertStatus(200);
    }

    /** @test */
    public function guests_can_not_view_account_api_listings()
    {
        $account = create('App\Account');

        $response = $this->get("/api/accounts");

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function an_authorized_user_can_view_account_api_listings()
    {
        $this->signIn();

        $account = create('App\Account', [
            'domain' => 'mytestsite.com',
            'ip'     => '255.1.1.100',
        ]);

        $response = $this->get("/api/accounts");

        $response->assertStatus(200);

        tap($response->json(), function ($accounts) {
            $this->assertCount(1, $accounts);
            $this->assertEquals('mytestsite.com', $accounts[0]['domain']);
            $this->assertEquals('255.1.1.100', $accounts[0]['ip']);
        });
    }

    /** @test */
    public function the_account_listings_are_in_alphabetical_order()
    {
        $this->signIn();

        $accountA = create('App\Account', ['domain' => 'somesite.com']);
        $accountB = create('App\Account', ['domain' => 'anothersite.com']);
        $accountC = create('App\Account', ['domain' => 'thelastsite.com']);

        $response = $this->get("/api/accounts");

        $response->assertStatus(200);

        $response->jsonData()->assertEquals([
            $accountB,
            $accountA,
            $accountC
        ]);
    }

    /** @test */
    public function the_account_listings_can_be_filtered_by_server()
    {
        $this->withoutExceptionHandling();
        $this->signIn();

        $serverA = create('App\Server', [
            'server_type' => 'vps'
        ]);
        $serverB = create('App\Server', [
            'server_type' => 'dedicated'
        ]);

        $accountA = create('App\Account', [
            'server_id' => $serverA->id,
            'domain' => 'somedomain.com'
        ]);
        $accountB = create('App\Account', [
            'server_id' => $serverB->id,
            'domain' => 'anotherdomain.com'
        ]);


        $response = $this->get("/api/accounts/{$serverA->id}");

        $response->assertStatus(200);

        tap($response->json(), function ($accounts) {
            $this->assertCount(1, $accounts);
            $this->assertEquals('somedomain.com', $accounts[0]['domain']);
        });
    }
}
