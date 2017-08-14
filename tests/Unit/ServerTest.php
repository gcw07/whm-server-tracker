<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServerTest extends TestCase
{
    use RefreshDatabase;

    protected $server;

    public function setUp()
    {
        parent::setUp();

        $this->server = create('App\Server');
    }

    /** @test */
    public function a_server_has_accounts()
    {
        $this->assertInstanceOf(
            'Illuminate\Database\Eloquent\Collection', $this->server->accounts
        );
    }

    /** @test */
    public function a_server_can_add_an_account()
    {
        $this->server->addAccount([
            'domain'         => 'my-server-name.com',
            'user'           => 'my-server',
            'ip'             => '1.1.1.1',
            'backup'         => true,
            'suspended'      => false,
            'suspend_reason' => 'not suspended',
            'suspend_time'   => null,
            'setup_date'     => Carbon::parse('-1 month'),
            'disk_used'      => '500M',
            'disk_limit'     => '2000M',
            'plan'           => '2 Gig'
        ]);

        $this->assertCount(1, $this->server->accounts);
    }

    /** @test */
    public function a_server_can_remove_an_account()
    {
        $account = create('App\Account', [
            'server_id' => $this->server->id
        ]);

        $this->server->removeAccount($account);

        $this->assertCount(0, $this->server->accounts);
    }

    /** @test */
    public function it_will_add_an_account_if_it_does_not_exist()
    {
        $accounts = [
            [
                'domain'        => 'my-site.com',
                'user'          => 'mysite',
                'ip'            => '1.1.1.1',
                'backup'        => 1,
                'suspended'     => 0,
                'suspendreason' => 'not suspended',
                'suspendtime'   => 0,
                'startdate'     => '17 Jan 1 10:35',
                'diskused'      => '300M',
                'disklimit'     => '2000M',
                'plan'          => '2 Gig',
            ]
        ];

        $this->server->processAccounts($accounts);

        $this->assertCount(1, $this->server->fresh()->accounts);
    }

    /** @test */
    public function it_will_update_an_account_if_it_exists()
    {
        $account = create('App\Account', [
            'server_id' => $this->server->id,
            'domain'    => 'my-site.com',
            'user'      => 'mysite'
        ]);

        $accounts = [
            [
                'domain'        => 'my-site.com',
                'user'          => 'mysite',
                'ip'            => '1.1.1.1',
                'backup'        => 1,
                'suspended'     => 0,
                'suspendreason' => 'not suspended',
                'suspendtime'   => 0,
                'startdate'     => '17 Jan 1 10:35',
                'diskused'      => '300M',
                'disklimit'     => '2000M',
                'plan'          => '2 Gig',
            ]
        ];

        $this->server->processAccounts($accounts);

        tap($this->server->fresh(), function ($server) {
            $this->assertCount(1, $server->accounts);
            $this->assertEquals('my-site.com', $server->accounts->first()->domain);
            $this->assertEquals('mysite', $server->accounts->first()->user);
        });
    }

    /** @test */
    public function it_will_skip_over_ignored_usernames()
    {
        $validAccount = [
            'domain'        => 'my-site.com',
            'user'          => 'mysite',
            'ip'            => '1.1.1.1',
            'backup'        => 1,
            'suspended'     => 0,
            'suspendreason' => 'not suspended',
            'suspendtime'   => 0,
            'startdate'     => '17 Jan 1 10:35',
            'diskused'      => '300M',
            'disklimit'     => '2000M',
            'plan'          => '2 Gig',
        ];

        $skipAccount = [
            'domain'        => 'gwscripts.com',
            'user'          => 'gwscripts',
            'ip'            => '1.1.1.1',
            'backup'        => 1,
            'suspended'     => 0,
            'suspendreason' => 'not suspended',
            'suspendtime'   => 0,
            'startdate'     => '17 Jan 20 9:35',
            'diskused'      => '300M',
            'disklimit'     => '2000M',
            'plan'          => '2 Gig',
        ];

        $accounts = [$validAccount, $skipAccount];

        $this->server->processAccounts($accounts);

        tap($this->server->fresh(), function ($server) {
            $this->assertCount(1, $server->accounts);
        });
    }

    /** @test */
    public function it_will_remove_accounts_that_no_longer_exists_on_server()
    {
        $account1 = create('App\Account', [
            'server_id' => $this->server->id,
            'domain'    => 'first-site.com',
            'user'      => 'firstsite'
        ]);

        $account2 = create('App\Account', [
            'server_id' => $this->server->id,
            'domain'    => 'site-to-remove.com',
            'user'      => 'sitetoremove'
        ]);

        $accounts = [
            [
                'domain'        => 'first-site.com',
                'user'          => 'firstsite',
                'ip'            => '1.1.1.1',
                'backup'        => 1,
                'suspended'     => 0,
                'suspendreason' => 'not suspended',
                'suspendtime'   => 0,
                'startdate'     => '17 Jan 1 10:35',
                'diskused'      => '300M',
                'disklimit'     => '2000M',
                'plan'          => '2 Gig',
            ]
        ];

        $this->server->processAccounts($accounts);

        tap($this->server->fresh(), function ($server) {
            $this->assertCount(1, $server->accounts);
        });
    }

    /** @test */
    public function can_get_formatted_server_types()
    {
        $serverA = make('App\Server', ['server_type' => 'vps']);
        $serverB = make('App\Server', ['server_type' => 'dedicated']);
        $serverC = make('App\Server', ['server_type' => 'reseller']);

        $this->assertEquals('VPS', $serverA->formatted_server_type);
        $this->assertEquals('Dedicated', $serverB->formatted_server_type);
        $this->assertEquals('Reseller', $serverC->formatted_server_type);
    }

    /** @test */
    public function can_determine_a_missing_api_token()
    {
        $serverValidToken = make('App\Server', ['server_type' => 'vps', 'token' => 'valid-token']);
        $serverNoToken = make('App\Server', ['server_type' => 'vps']);
        $serverTypeNeedsNoToken = make('App\Server', ['server_type' => 'reseller']);

        $this->assertFalse($serverValidToken->missing_token);
        $this->assertTrue($serverNoToken->missing_token);
        $this->assertFalse($serverTypeNeedsNoToken->missing_token);
    }

    /** @test */
    public function can_determine_if_it_can_refresh_external_data()
    {
        $serverValidToken = make('App\Server', ['server_type' => 'vps', 'token' => 'valid-token']);
        $serverNoToken = make('App\Server', ['server_type' => 'vps']);
        $serverTypeNeedsNoToken = make('App\Server', ['server_type' => 'reseller']);

        $this->assertTrue($serverValidToken->can_refresh_data);
        $this->assertFalse($serverNoToken->can_refresh_data);
        $this->assertFalse($serverTypeNeedsNoToken->can_refresh_data);
    }

    /** @test */
    public function can_get_whm_external_url()
    {
        $serverA = make('App\Server', ['address' => '1.1.1.1', 'port' => 2087]);
        $serverB = make('App\Server', ['address' => '3.3.3.3', 'port' => 2086]);

        $this->assertEquals('https://1.1.1.1:2087', $serverA->whm_url);
        $this->assertEquals('http://3.3.3.3:2086', $serverB->whm_url);
    }

    /** @test */
    public function the_api_token_should_not_be_included_in_returned_data()
    {
        $server = make('App\Server', ['server_type' => 'vps', 'token' => 'valid-token']);

        $this->assertArrayNotHasKey('token', $server->toArray());
    }
}
