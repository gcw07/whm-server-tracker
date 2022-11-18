<?php

namespace App\Console\Commands;

use App\Models\Monitor;
use Illuminate\Console\Command;

class CheckBlacklistCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server-tracker:check-blacklist
                           {--url= : Only check these urls}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for being blacklisted for all sites.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $monitors = Monitor::query()
            ->where('certificate_check_enabled', true)
            ->orderBy('url')
            ->get();

        if ($url = $this->option('url')) {
            $monitors = $monitors->filter(fn(Monitor $monitor) => in_array((string) $monitor->url, explode(',', $url)));
        }

        $this->comment('Start checking the blacklist of '.count($monitors).' monitors...');

        $monitors->each(function (Monitor $monitor) {
            $this->info("Checking blacklist for {$monitor->url}");

            $monitor->checkBlacklist();
//
//            if ($monitor->certificate_status !== CertificateStatus::VALID) {
//                $this->error("Could not download certificate of {$monitor->url} because: {$monitor->certificate_check_failure_reason}");
//            }
        });

        $this->info('All done!');
    }
}
