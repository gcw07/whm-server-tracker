<?php

namespace App\Console\Commands;

use App\Models\CloudflareAnalytic;
use App\Models\MonitorCloudflareCheck;
use App\Services\Cloudflare\CloudflareApiClient;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

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
            ->with('monitor')
            ->get();

        $this->comment("Fetching Cloudflare analytics for {$checks->count()} zone(s) on {$date}...");

        /** @var array<string, int> $zoneToCheckId Maps zone ID → monitor_cloudflare_check_id */
        $zoneToCheckId = $checks->pluck('id', 'cloudflare_zone_id')->all();

        /** @var array<string, string> $zoneToUrl Maps zone ID → monitor URL */
        $zoneToUrl = $checks->pluck('monitor.url', 'cloudflare_zone_id')->all();

        Log::info('Fetching Cloudflare analytics', [
            'date' => $date,
            'zones' => collect($zoneToUrl)->map(fn (string $url, string $zoneId) => [
                'zone_id' => $zoneId,
                'monitor_url' => $url,
            ])->values()->all(),
        ]);

        $analytics = $client->fetchDailyAnalytics(array_keys($zoneToCheckId), $date);

        foreach ($zoneToCheckId as $zoneId => $checkId) {
            if ($analytics->has($zoneId)) {
                $data = $analytics->get($zoneId);
                Log::info('Analytics received for zone', [
                    'zone_id' => $zoneId,
                    'monitor_url' => $zoneToUrl[$zoneId] ?? null,
                    'unique_visitors' => $data['unique_visitors'],
                    'requests_total' => $data['requests_total'],
                    'bandwidth_total' => $data['bandwidth_total'],
                ]);
            } else {
                Log::warning('No analytics returned for zone', [
                    'zone_id' => $zoneId,
                    'monitor_url' => $zoneToUrl[$zoneId] ?? null,
                ]);
            }
        }

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

        Log::info('Cloudflare analytics fetch complete', [
            'date' => $date,
            'saved' => count($rows),
            'queried' => count($zoneToCheckId),
        ]);

        $this->info('Saved analytics for '.count($rows).' zone(s).');
    }
}
