<?php

namespace App\Console\Commands;

use App\Models\Server;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('server-tracker:refresh')]
#[Description('Refresh remote server data.')]
class RefreshCommand extends Command
{
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        $this->info('Refreshing remote server data');

        Server::refreshData();
    }
}
