<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Server;
use Illuminate\Database\Seeder;

class ServersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Server::factory()
            ->has(Account::factory()->count(15))
            ->count(10)
            ->create();
    }
}
