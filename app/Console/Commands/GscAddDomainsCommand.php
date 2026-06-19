<?php

namespace App\Console\Commands;

use App\Jobs\AddDomainToSearchConsoleJob;
use App\Models\MonitorSearchConsoleCheck;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('server-tracker:gsc-add-domains {--dry-run : Log what would be added without making any API calls}')]
#[Description('Add missing domain properties to Google Search Console for monitors on Cloudflare.')]
class GscAddDomainsCommand extends Command
{
    public function handle(): void
    {
        $checks = MonitorSearchConsoleCheck::where('has_domain_property', false)->get();

        if ($checks->isEmpty()) {
            $this->info('All monitors already have a domain property in Google Search Console.');

            return;
        }

        if ($this->option('dry-run')) {
            $this->comment("Dry run — {$checks->count()} domain(s) would be added to Google Search Console:");
            $checks->each(fn ($check) => $this->line("  {$check->domain_property}"));

            return;
        }

        $this->info("Dispatching {$checks->count()} job(s) to add domain properties (staggered 4s apart)...");

        $checks->each(function ($check, $index) {
            AddDomainToSearchConsoleJob::dispatch($check)
                ->delay(now()->addSeconds($index * 4));
        });

        $this->info('Done.');
    }
}
