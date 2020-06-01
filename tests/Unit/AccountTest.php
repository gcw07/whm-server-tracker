<?php

namespace Tests\Unit;

use App\Models\Server;
use Tests\Factories\AccountFactory;
use Tests\Factories\ServerFactory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_a_server()
    {
        $server = ServerFactory::new()->create();
        $account = AccountFactory::new()->create(['server_id' => $server->id]);

        $this->assertInstanceOf(Server::class, $account->server);
    }

    /** @test */
    public function can_get_cpanel_external_url()
    {
        $account = AccountFactory::new()->make(['domain' => 'mydomain.com']);

        $this->assertEquals('https://mydomain.com/cpanel', $account->cpanel_url);
    }

    /** @test */
    public function can_get_whm_external_url()
    {
        $server = ServerFactory::new()->create(['address' => '1.1.1.1', 'port' => 2087]);
        $account = AccountFactory::new()->make(['server_id' => $server->id, 'domain' => 'mydomain.com']);

        $this->assertEquals('https://1.1.1.1:2087', $account->whm_url);
    }

    /** @test */
    public function can_get_domain_external_url()
    {
        $account = AccountFactory::new()->make(['domain' => 'mydomain.com']);

        $this->assertEquals('http://mydomain.com', $account->domain_url);
    }

    /** @test */
    public function can_get_disk_usage()
    {
        $accountA = AccountFactory::new()->make(['disk_used' => '300M', 'disk_limit' => '2000M']);
        $accountB = AccountFactory::new()->make(['disk_used' => '350M', 'disk_limit' => '2000M']);
        $accountC = AccountFactory::new()->make(['disk_used' => '400M', 'disk_limit' => 'unlimited']);

        $this->assertEquals('15%', $accountA->disk_usage);
        $this->assertEquals('17.5%', $accountB->disk_usage);
        $this->assertEquals('n/a', $accountC->disk_usage);
    }
}
