<?php

namespace App\Console\Commands;

use App\Jobs\AddDomainToSearchConsoleJob;
use App\Jobs\AddGscDnsRecordToCloudflareJob;
use App\Jobs\FetchGoogleSearchConsoleSitesJob;
use App\Jobs\FetchSearchConsoleDnsTxtJob;
use App\Models\MonitorSearchConsoleCheck;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('server-tracker:gsc-sync')]
#[Description('Run the full Google Search Console sync pipeline: fetch sites, add missing domains, fetch DNS tokens, add Cloudflare DNS records.')]
class SyncGoogleSearchConsoleCommand extends Command
{
    public function handle(): void
    {
        // Step 1: fetch GSC site list and upsert local records (runs synchronously).
        $this->info('Step 1: Fetching Google Search Console site list...');
        dispatch_sync(new FetchGoogleSearchConsoleSitesJob);
        $this->info('Done.');

        // Step 2: add any monitors missing a domain property. Jobs chain to steps 3 & 4.
        $step2 = MonitorSearchConsoleCheck::where('has_domain_property', false)->get();

        if ($step2->isNotEmpty()) {
            $this->info("Step 2: Dispatching {$step2->count()} job(s) to add missing domain properties...");
            $step2->each(function ($check, $index) {
                AddDomainToSearchConsoleJob::dispatch($check)
                    ->delay(now()->addSeconds($index * 4));
            });
        }

        // Catch-up: monitors that have a domain property but no DNS token yet.
        $step3 = MonitorSearchConsoleCheck::where('has_domain_property', true)
            ->whereNull('dns_txt_record')
            ->get();

        if ($step3->isNotEmpty()) {
            $this->info("Step 3 catch-up: Dispatching {$step3->count()} job(s) to fetch missing DNS tokens...");
            $step3->each(function ($check, $index) {
                FetchSearchConsoleDnsTxtJob::dispatch($check)
                    ->delay(now()->addSeconds($index * 4));
            });
        }

        // Catch-up: monitors that have a DNS token but it hasn't been added to Cloudflare yet.
        $step4 = MonitorSearchConsoleCheck::whereNotNull('dns_txt_record')
            ->where('dns_added_to_cloudflare', false)
            ->with('monitor.cloudflareCheck')
            ->get();

        if ($step4->isNotEmpty()) {
            $this->info("Step 4 catch-up: Dispatching {$step4->count()} job(s) to add Cloudflare DNS records...");
            $step4->each(function ($check, $index) {
                AddGscDnsRecordToCloudflareJob::dispatch($check)
                    ->delay(now()->addSeconds($index * 2));
            });
        }

        if ($step2->isEmpty() && $step3->isEmpty() && $step4->isEmpty()) {
            $this->info('All monitors are fully synced. Nothing to do.');
        }
    }
}
