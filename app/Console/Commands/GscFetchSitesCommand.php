<?php

namespace App\Console\Commands;

use App\Jobs\FetchGoogleSearchConsoleSitesJob;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('server-tracker:gsc-fetch-sites')]
#[Description('Fetch Google Search Console domain properties and update local records for monitors on Cloudflare.')]
class GscFetchSitesCommand extends Command
{
    public function handle(): void
    {
        $this->info('Dispatching job to fetch Google Search Console sites...');

        FetchGoogleSearchConsoleSitesJob::dispatch();

        $this->info('Job dispatched.');
    }
}
