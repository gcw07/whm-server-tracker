<?php

namespace Tests\Unit;

use Tests\Factories\UserFactory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function email_address_should_be_lowercase()
    {
        $user = UserFactory::new()->create([
            'email' => 'JOHN@example.COM'
        ]);

        $this->assertEquals('john@example.com', $user->email);
    }
}
