<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_server_can_add_an_account()
    {
        $server = create('App\Server');

        $server->addAccount([
            'domain'         => 'my-server-name.com',
            'user'           => 'my-server',
            'backup'         => true,
            'suspended'      => false,
            'suspend_reason' => 'not suspended',
            'suspend_time'   => null,
            'setup_date'     => Carbon::parse('-1 month'),
            'disk_used'      => '500M',
            'disk_limit'     => '2000M',
            'plan'           => '2 Gig'
        ]);

        $this->assertCount(1, $server->accounts);
    }
}
