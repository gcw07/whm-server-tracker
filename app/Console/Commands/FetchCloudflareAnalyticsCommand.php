<?php

namespace App\Console\Commands;

use App\Models\CloudflareAnalytic;
use App\Models\MonitorCloudflareCheck;
use App\Services\Cloudflare\CloudflareApiClient;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('server-tracker:fetch-cloudflare-analytics
                    {--date= : Date to fetch in Y-m-d format, defaults to yesterday}')]
#[Description('Fetch daily analytics from the Cloudflare GraphQL API for all zones.')]
class FetchCloudflareAnalyticsCommand extends Command
{
    public function handle(CloudflareApiClient $client): void
    {
        $date = $this->option('date') ?? now()->subDay()->toDateString();

        $checks = MonitorCloudflareCheck::query()
            ->where('enabled', true)
            ->whereNotNull('cloudflare_zone_id')
            ->get();

        $this->comment("Fetching Cloudflare analytics for {$checks->count()} zone(s) on {$date}...");

        /** @var array<string, int> $zoneToCheckId Maps zone ID → monitor_cloudflare_check_id */
        $zoneToCheckId = $checks->pluck('id', 'cloudflare_zone_id')->all();

        $analytics = $client->fetchDailyAnalytics(array_keys($zoneToCheckId), $date);

        $rows = $analytics->map(function (array $data, string $zoneId) use ($zoneToCheckId, $date) {
            return array_merge($data, [
                'monitor_cloudflare_check_id' => $zoneToCheckId[$zoneId],
                'date' => $date,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        })->values()->all();

        if (empty($rows)) {
            $this->warn('No analytics data returned from Cloudflare.');

            return;
        }

        CloudflareAnalytic::upsert(
            $rows,
            ['monitor_cloudflare_check_id', 'date'],
            ['unique_visitors', 'requests_total', 'bandwidth_total', 'updated_at'],
        );

        $this->info('Saved analytics for '.count($rows).' zone(s).');
    }
}
