<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server-tracker:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Server Tracker application.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (! $this->migratedTables()) {
            $this->warn('The database tables have not been migrated. Please migrate the database.');

            return;
        }

        if ($this->isApplicationInstalled()) {
            $this->warn('Application already installed.');

            return;
        }

        $this->line('Server Tracker Installation.');
        $this->line('This will install a default user for the site.');

        $this->installApplication();
        $this->info('Installed application successfully.');
    }

    private function migratedTables()
    {
        if (Schema::hasTable('users')) {
            return true;
        }

        return false;
    }

    private function isApplicationInstalled()
    {
        if (DB::table('users')->count() > 0) {
            return true;
        }

        return false;
    }

    private function installApplication()
    {
        $name = $this->ask('Enter your full name:');
        $email = $this->ask('Enter your email address:');
        $password = $this->secret('Enter your password:');
        $confirmPassword = $this->secret('Confirm password:');

        if ($password != $confirmPassword) {
            $this->error('Your passwords do not match. Please try again.');
            exit;
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password),
        ]);
    }
}
