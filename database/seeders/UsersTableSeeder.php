<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = factory('App\Models\User')->create([
            'name' => 'Grant Williams',
            'email' => 'grant@example.com',
            'password' => bcrypt('secret')
        ]);
    }
}