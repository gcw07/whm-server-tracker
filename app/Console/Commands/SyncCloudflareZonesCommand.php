<?php

namespace App\Console\Commands;

use App\Models\Monitor;
use App\Services\Cloudflare\CloudflareApiClient;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('server-tracker:sync-cloudflare-zones')]
#[Description('Sync Cloudflare zone IDs for monitors that are on Cloudflare.')]
class SyncCloudflareZonesCommand extends Command
{
    public function handle(CloudflareApiClient $client): void
    {
        $zones = $client->fetchAllZones();

        $monitors = Monitor::query()
            ->whereHas('domainCheck', fn ($q) => $q->where('is_on_cloudflare', true))
            ->whereHas('cloudflareCheck', fn ($q) => $q->where('enabled', true))
            ->with('cloudflareCheck')
            ->orderBy('url')
            ->get();

        $this->comment("Syncing Cloudflare zones for {$monitors->count()} monitor(s)...");

        $synced = 0;

        $monitors->each(function (Monitor $monitor) use ($zones, &$synced) {
            $zone = $zones->get($monitor->domain_name);

            if ($zone === null) {
                return;
            }

            $monitor->cloudflareCheck->update([
                'cloudflare_zone_id' => $zone['id'],
                'cloudflare_account_id' => $zone['account_id'],
                'zone_status' => $zone['status'],
                'last_synced_at' => now(),
            ]);

            $synced++;
        });

        $this->info("Synced {$synced} Cloudflare zone(s).");
    }
}
