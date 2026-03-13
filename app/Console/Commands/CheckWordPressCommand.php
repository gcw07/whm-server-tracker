<?php

namespace App\Console\Commands;

use App\Jobs\CheckWordPressJob;
use App\Models\Monitor;
use Illuminate\Console\Command;

class CheckWordPressCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server-tracker:check-wordpress
                           {--url= : Only check these urls}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if the monitor URL is using WordPress and get the version.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        $monitors = Monitor::query()
            ->whereHas('wordpressCheck', fn ($q) => $q->where('enabled', true))
            ->with('wordpressCheck')
            ->orderBy('url')
            ->get();

        if ($url = $this->option('url')) {
            $monitors = $monitors->filter(fn (Monitor $monitor) => in_array((string) $monitor->url, explode(',', $url)));
        }

        $this->comment('Start checking WordPress for '.count($monitors).' monitors...');

        $monitors->each(function (Monitor $monitor) {
            $this->info("Checking WordPress for {$monitor->url}");

            dispatch(new CheckWordPressJob($monitor));
        });

        $this->info('All done!');
    }
}
