<?php

namespace App\Console\Commands;

use App\Services\Google\GoogleApiClientService;
use Google\Service\SearchConsole;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('server-tracker:gsc-list-sites')]
#[Description('List all domain properties in Google Search Console (read-only debug utility).')]
class GscListSitesCommand extends Command
{
    public function handle(GoogleApiClientService $googleClient): void
    {
        $client = $googleClient->getClient();
        $gscService = new SearchConsole($client);

        $siteList = $gscService->sites->listSites();

        $domainProperties = collect($siteList->getSiteEntry() ?? [])
            ->filter(fn ($site) => str_starts_with($site->getSiteUrl(), 'sc-domain:'))
            ->map(fn ($site) => $site->getSiteUrl())
            ->values();

        $this->info("Found {$domainProperties->count()} domain properties in Google Search Console:");
        $domainProperties->each(fn ($domain) => $this->line("  {$domain}"));
    }
}
