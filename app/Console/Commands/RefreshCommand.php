<?php

namespace App\Console\Commands;

use App\Models\Server;
use Illuminate\Console\Command;

class RefreshCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server-tracker:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh remote server data.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Refreshing remote server data');

        Server::refreshData();
    }
}
