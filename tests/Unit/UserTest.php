<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function email_address_should_be_lowercase()
    {
        $user = User::factory()->create([
            'email' => 'JOHN@example.COM'
        ]);

        $this->assertEquals('john@example.com', $user->email);
    }
}
