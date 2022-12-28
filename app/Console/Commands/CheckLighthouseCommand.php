<?php

namespace App\Console\Commands;

use App\Jobs\CheckLighthouseJob;
use App\Models\Monitor;
use Illuminate\Console\Command;

class CheckLighthouseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server-tracker:check-lighthouse
                           {--url= : Only check these urls}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for running lighthouse for all sites.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $monitors = Monitor::query()
            ->where('lighthouse_check_enabled', true)
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
