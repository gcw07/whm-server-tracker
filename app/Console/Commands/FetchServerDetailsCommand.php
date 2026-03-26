<?php

namespace App\Console\Commands;

use App\Jobs\FetchServerDetailsJob;
use App\Models\Server;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('server-tracker:fetch-server-details')]
#[Description('Fetch details for all servers.')]
class FetchServerDetailsCommand extends Command
{
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        $this->info('Fetching server details...');

        Server::query()->withTokens()->get()
            ->each(fn (Server $server) => dispatch(new FetchServerDetailsJob($server)));
    }
}
