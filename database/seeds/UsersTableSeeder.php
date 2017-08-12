<?php

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
        $user = factory('App\User')->create([
            'name' => 'Grant Williams',
            'email' => 'grant@gwscripts.com',
            'password' => bcrypt('secret')
        ]);
    }
}
