<?php

namespace App\Console\Commands;

use App\Jobs\CheckDomainNameJob;
use App\Models\Monitor;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('server-tracker:check-domain-name
                           {--url= : Only check these urls}')]
#[Description('Check domain name expiration date for all sites.')]
class CheckDomainNameCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $monitors = Monitor::query()
            ->whereHas('domainCheck', fn ($q) => $q->where('enabled', true))
            ->with('domainCheck')
            ->orderBy('url')
            ->get();

        if ($url = $this->option('url')) {
            $monitors = $monitors->filter(fn (Monitor $monitor) => in_array((string) $monitor->url, explode(',', $url)));
        }

        $this->comment('Start checking the domain name expiration date of '.count($monitors).' monitors...');

        $monitors->each(function (Monitor $monitor) {
            $this->info("Checking domain name expiration for {$monitor->url}");

            dispatch(new CheckDomainNameJob($monitor))->onQueue('low');
        });

        $this->info('All done!');
    }
}
