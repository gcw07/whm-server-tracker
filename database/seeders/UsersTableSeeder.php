<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Grant Williams',
            'email' => 'grant@example.com',
            'password' => bcrypt('secret'),
            'notification_types' => [
                'uptime_check_failed' => false,
                'uptime_check_succeeded' => false,
                'uptime_check_recovered' => false,
                'certificate_check_succeeded' => false,
                'certificate_check_failed' => false,
                'certificate_expires_soon' => false,
                'fetched_server_data_succeeded' => false,
                'fetched_server_data_failed' => false,
            ],
        ]);
    }
}
