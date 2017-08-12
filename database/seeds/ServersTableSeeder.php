<?php

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
        $servers = factory('App\Server', 10)->create();

        foreach ($servers as $server) {
            factory('App\Account', 15)->create([
                'server_id' => $server->id
            ]);
        }
    }
}
