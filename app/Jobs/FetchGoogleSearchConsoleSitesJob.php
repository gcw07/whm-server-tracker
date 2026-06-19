<?php

namespace App\Jobs;

use App\Models\Monitor;
use App\Models\MonitorSearchConsoleCheck;
use App\Services\Google\GoogleApiClientService;
use Google\Service\SearchConsole;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

#[Tries(3)]
class FetchGoogleSearchConsoleSitesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(GoogleApiClientService $googleClient): void
    {
        $client = $googleClient->getClient();
        $gscService = new SearchConsole($client);

        $siteList = $gscService->sites->listSites();

        $gscDomains = collect($siteList->getSiteEntry() ?? [])
            ->filter(fn ($site) => str_starts_with($site->getSiteUrl(), 'sc-domain:'))
            ->map(fn ($site) => str_replace('sc-domain:', '', $site->getSiteUrl()))
            ->flip()
            ->all();

        $monitors = Monitor::query()
            ->whereHas('cloudflareCheck', fn ($q) => $q->whereNotNull('cloudflare_zone_id'))
            ->get();

        $upsertData = $monitors->map(function (Monitor $monitor) use ($gscDomains) {
            $domain = $this->extractDomain((string) $monitor->url);

            return [
                'monitor_id' => $monitor->id,
                'has_domain_property' => array_key_exists($domain, $gscDomains),
                'domain_property' => "sc-domain:{$domain}",
                'last_synced_at' => now(),
            ];
        })->all();

        MonitorSearchConsoleCheck::upsert(
            $upsertData,
            ['monitor_id'],
            ['has_domain_property', 'domain_property', 'last_synced_at'],
        );
    }

    private function extractDomain(string $url): string
    {
        $host = parse_url($url, PHP_URL_HOST) ?? $url;

        return preg_replace('/^www\./', '', $host);
    }
}
