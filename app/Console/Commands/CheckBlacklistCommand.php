<?php

namespace App\Console\Commands;

use App\Jobs\CheckBlacklistJob;
use App\Models\Monitor;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('server-tracker:check-blacklist
                           {--url= : Only check these urls}')]
#[Description('Check for being blacklisted for all sites.')]
class CheckBlacklistCommand extends Command
{
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        $monitors = Monitor::query()
            ->whereHas('blacklistCheck', fn ($q) => $q->where('enabled', true))
            ->with('blacklistCheck')
            ->orderBy('url')
            ->get();

        if ($url = $this->option('url')) {
            $monitors = $monitors->filter(fn (Monitor $monitor) => in_array((string) $monitor->url, explode(',', $url)));
        }

        $this->comment('Start checking the blacklist of '.count($monitors).' monitors...');

        $monitors->each(function (Monitor $monitor) {
            $this->info("Checking blacklist for {$monitor->url}");

            dispatch(new CheckBlacklistJob($monitor));
        });

        $this->info('All done!');
    }
}
