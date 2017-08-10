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

}
