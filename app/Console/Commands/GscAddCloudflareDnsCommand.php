<?php

namespace App\Console\Commands;

use App\Jobs\AddGscDnsRecordToCloudflareJob;
use App\Models\MonitorSearchConsoleCheck;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('server-tracker:gsc-add-cloudflare-dns {--dry-run : Log what would be added without making any API calls}')]
#[Description('Add Google Search Console verification TXT records to Cloudflare DNS.')]
class GscAddCloudflareDnsCommand extends Command
{
    public function handle(): void
    {
        $checks = MonitorSearchConsoleCheck::where('dns_added_to_cloudflare', false)
            ->whereNotNull('dns_txt_record')
            ->with('monitor.cloudflareCheck')
            ->get();

        if ($checks->isEmpty()) {
            $this->info('All DNS TXT records have already been added to Cloudflare.');

            return;
        }

        if ($this->option('dry-run')) {
            $this->comment("Dry run — {$checks->count()} TXT record(s) would be added to Cloudflare DNS:");
            $checks->each(fn ($check) => $this->line(
                "  {$check->domain_property} => {$check->dns_txt_record}"
            ));

            return;
        }

        $this->info("Dispatching {$checks->count()} job(s) to add Cloudflare DNS records (staggered 2s apart)...");

        $checks->each(function ($check, $index) {
            AddGscDnsRecordToCloudflareJob::dispatch($check)
                ->delay(now()->addSeconds($index * 2));
        });

        $this->info('Done.');
    }
}
