<?php

namespace App\Console\Commands;

use App\Jobs\FetchSearchConsoleDnsTxtJob;
use App\Models\MonitorSearchConsoleCheck;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('server-tracker:gsc-fetch-dns')]
#[Description('Fetch Google Site Verification DNS TXT records for all monitors in Search Console.')]
class GscFetchDnsCommand extends Command
{
    public function handle(): void
    {
        $checks = MonitorSearchConsoleCheck::where('has_domain_property', true)
            ->whereNull('dns_txt_record')
            ->get();

        if ($checks->isEmpty()) {
            $this->info('All monitors already have a DNS TXT record stored.');

            return;
        }

        $this->info("Dispatching {$checks->count()} job(s) to fetch DNS TXT records (staggered 4s apart)...");

        $checks->each(function ($check, $index) {
            FetchSearchConsoleDnsTxtJob::dispatch($check)
                ->delay(now()->addSeconds($index * 4));
        });

        $this->info('Done.');
    }
}
