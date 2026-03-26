<?php

namespace App\Console\Commands;

use App\Jobs\CheckLighthouseJob;
use App\Models\Monitor;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('server-tracker:check-lighthouse
                           {--url= : Only check these urls}')]
#[Description('Check for running lighthouse for all sites.')]
class CheckLighthouseCommand extends Command
{
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        $monitors = Monitor::query()
            ->whereHas('lighthouseCheck', fn ($q) => $q->where('enabled', true))
            ->with('lighthouseCheck')
            ->orderBy('url')
            ->get();

        if ($url = $this->option('url')) {
            $monitors = $monitors->filter(fn (Monitor $monitor) => in_array((string) $monitor->url, explode(',', $url)));
        }

        $this->comment('Start checking the lighthouse of '.count($monitors).' monitors...');

        $monitors->each(function (Monitor $monitor) {
            $this->info("Checking lighthouse for {$monitor->url}");

            dispatch(new CheckLighthouseJob($monitor));
        });

        $this->info('All done!');
    }
}
