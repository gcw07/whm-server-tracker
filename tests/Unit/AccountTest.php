<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\Server;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_a_server()
    {
        $server = Server::factory()->create();
        $account = Account::factory()->create(['server_id' => $server->id]);

        $this->assertInstanceOf(Server::class, $account->server);
    }

    /** @test */
    public function can_get_cpanel_external_url()
    {
        $account = Account::factory()->make(['domain' => 'mydomain.com']);

        $this->assertEquals('https://mydomain.com/cpanel', $account->cpanel_url);
    }

    /** @test */
    public function can_get_whm_external_url()
    {
        $server = Server::factory()->create(['address' => '1.1.1.1', 'port' => 2087]);
        $account = Account::factory()->make(['server_id' => $server->id, 'domain' => 'mydomain.com']);

        $this->assertEquals('https://1.1.1.1:2087', $account->whm_url);
    }

    /** @test */
    public function can_get_domain_external_url()
    {
        $account = Account::factory()->make(['domain' => 'mydomain.com']);

        $this->assertEquals('http://mydomain.com', $account->domain_url);
    }

    /** @test */
    public function can_get_disk_usage()
    {
        $accountA = Account::factory()->make(['disk_used' => '300M', 'disk_limit' => '2000M']);
        $accountB = Account::factory()->make(['disk_used' => '350M', 'disk_limit' => '2000M']);
        $accountC = Account::factory()->make(['disk_used' => '400M', 'disk_limit' => 'unlimited']);

        $this->assertEquals('15%', $accountA->disk_usage);
        $this->assertEquals('17.5%', $accountB->disk_usage);
        $this->assertEquals('n/a', $accountC->disk_usage);
    }
}
