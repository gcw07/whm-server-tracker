<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UpgradeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server-tracker:upgrade';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upgrade the Server Tracker application to v2.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (! $this->previousDatabaseSettings()) {
            $this->warn('The previous database .env settings have not been set. Please set these to run the upgrade process.');

            return;
        }

        if (! $this->canConnectToPreviousDatabase()) {
            $this->warn('Cannot connect to previous database.');

            return;
        }

        if ($this->isApplicationUpgraded()) {
            $this->warn('Application already upgraded.');

            return;
        }

        $this->line('Server Tracker Upgrade.');
        $this->line('This will upgrade the software to version 2.');
        $this->upgradeApplication();
        $this->info('Upgraded application successfully.');
    }

    protected function previousDatabaseSettings()
    {
        if (env('WHM_V1_DB_CONNECTION') !== '' &&
            env('WHM_V1_DB_HOST') !== '' &&
            env('WHM_V1_DB_PORT') !== '' &&
            env('WHM_V1_DB_DATABASE') !== '' &&
            env('WHM_V1_DB_USERNAME') !== '' &&
            env('WHM_V1_DB_PASSWORD') !== ''
        ) {
            return true;
        }

        return false;
    }

    protected function canConnectToPreviousDatabase(): bool
    {
        if (DB::connection('mysql_v1_db')->table('users')->count() > 0) {
            return true;
        }

        return false;
    }

    protected function isApplicationUpgraded(): bool
    {
        if (DB::table('users')->count() > 0) {
            return true;
        }

        return false;
    }

    protected function upgradeApplication()
    {
        $this->copyUsers();
        $this->copyServers();
    }

    protected function copyUsers()
    {
        $dataset = [];
        $users = DB::connection('mysql_v1_db')->table('users')->lazyById();

        foreach ($users as $user) {
            $dataset[] = [
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ];
        }

        DB::table('users')->insert($dataset);
    }

    protected function copyServers()
    {
        $dataset = [];
        $servers = DB::connection('mysql_v1_db')->table('servers')->lazyById();

        $key = $this->databaseEncryptionKey();
        $cipher = config('app.cipher');

        $encrypter = new Encrypter($key, $cipher);

        foreach ($servers as $server) {
            $dataset[] = [
                'name' => $server->name,
                'address' => $server->address,
                'port' => $server->port,
                'server_type' => $server->server_type,
                'token' => $encrypter->encryptString($server->token),
                'notes' => $server->notes,
                'created_at' => $server->created_at,
                'updated_at' => $server->updated_at,
            ];
        }

        DB::table('servers')->insert($dataset);
    }

    protected function databaseEncryptionKey(): ?string
    {
        $key = config('database.encryption_key');

        return base64_decode(Str::after($key, 'base64:'));
    }
}
