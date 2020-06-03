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
        $servers = factory('App\Models\Server', 10)->create();

        foreach ($servers as $server) {
            factory('App\Models\Account', 15)->create([
                'server_id' => $server->id
            ]);
        }
    }
}
