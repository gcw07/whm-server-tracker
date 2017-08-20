<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AccountTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function it_has_a_server()
    {
        $account = create('App\Account');

        $this->assertInstanceOf('App\Server', $account->server);
    }

    /** @test */
    public function can_get_cpanel_external_url()
    {
        $account = make('App\Account', ['domain' => 'mydomain.com']);

        $this->assertEquals('https://mydomain.com/cpanel', $account->cpanel_url);
    }

    /** @test */
    public function can_get_whm_external_url()
    {
        $server = create('App\Server', ['address' => '1.1.1.1', 'port' => 2087]);
        $account = make('App\Account', ['server_id' => $server->id, 'domain' => 'mydomain.com']);

        $this->assertEquals('https://1.1.1.1:2087', $account->whm_url);
    }

    /** @test */
    public function can_get_domain_external_url()
    {
        $account = make('App\Account', ['domain' => 'mydomain.com']);

        $this->assertEquals('http://mydomain.com', $account->domain_url);
    }

    /** @test */
    public function can_get_disk_usage()
    {
        $accountA = make('App\Account', ['disk_used' => '300M', 'disk_limit' => '2000M']);
        $accountB = make('App\Account', ['disk_used' => '350M', 'disk_limit' => '2000M']);

        $this->assertEquals('15%', $accountA->disk_usage);
        $this->assertEquals('17.5%', $accountB->disk_usage);
    }
}
