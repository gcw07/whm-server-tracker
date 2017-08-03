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
}
