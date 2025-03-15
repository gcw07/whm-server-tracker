<?php

namespace App\Console\Commands;

use App\Jobs\CheckDomainNameJob;
use App\Models\Monitor;
use Illuminate\Console\Command;

class CheckDomainNameCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server-tracker:check-domain-name
                           {--url= : Only check these urls}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check domain name expiration date for all sites.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $monitors = Monitor::query()
            ->where('domain_name_check_enabled', true)
            ->orderBy('url')
            ->get();

        if ($url = $this->option('url')) {
            $monitors = $monitors->filter(fn (Monitor $monitor) => in_array((string) $monitor->url, explode(',', $url)));
        }

        $this->comment('Start checking the domain name expiration date of '.count($monitors).' monitors...');

        $monitors->each(function (Monitor $monitor) {
            $this->info("Checking domain name expiration for {$monitor->url}");

            dispatch(new CheckDomainNameJob($monitor));
        });

        $this->info('All done!');
    }
}
