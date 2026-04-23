<?php

use App\Models\CloudflareAnalytic;
use App\Models\Monitor;
use App\Models\MonitorCloudflareCheck;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

uses(LazilyRefreshDatabase::class);

function cloudflareAnalyticsResponse(array $zones): array
{
    return [
        'data' => [
            'viewer' => [
                'zones' => $zones,
            ],
        ],
        'errors' => null,
    ];
}

function analyticsZone(string $zoneTag, int $uniques, int $requests, int $bytes): array
{
    return [
        'zoneTag' => $zoneTag,
        'httpRequests1dGroups' => [
            [
                'sum' => ['requests' => $requests, 'bytes' => $bytes],
                'uniq' => ['uniques' => $uniques],
            ],
        ],
    ];
}

function makeCheckWithZone(string $zoneId): MonitorCloudflareCheck
{
    $monitor = Monitor::create([
        'url' => "https://example-{$zoneId}.com",
        'uptime_check_enabled' => true,
        'certificate_check_enabled' => false,
    ]);

    $monitor->cloudflareCheck()->update(['cloudflare_zone_id' => $zoneId]);

    return $monitor->cloudflareCheck;
}

it('creates a cloudflare analytic record with correct data', function () {
    $check = makeCheckWithZone('zone-abc');

    Http::fake([
        'api.cloudflare.com/*' => Http::response(cloudflareAnalyticsResponse([
            analyticsZone('zone-abc', 500, 1000, 500_000),
        ])),
    ]);

    $this->artisan('server-tracker:fetch-cloudflare-analytics', ['--date' => '2026-04-12'])
        ->assertSuccessful();

    expect(CloudflareAnalytic::first())
        ->monitor_cloudflare_check_id->toBe($check->id)
        ->unique_visitors->toBe(500)
        ->requests_total->toBe(1000)
        ->bandwidth_total->toBe(500_000);
});

it('upserts on re-run for the same date', function () {
    $check = makeCheckWithZone('zone-upsert');

    // Pre-seed a record with stale data for the same date
    CloudflareAnalytic::create([
        'monitor_cloudflare_check_id' => $check->id,
        'date' => '2026-04-12',
        'unique_visitors' => 100,
        'requests_total' => 200,
        'bandwidth_total' => 300_000,
    ]);

    Http::fake([
        'api.cloudflare.com/*' => Http::response(cloudflareAnalyticsResponse([
            analyticsZone('zone-upsert', 999, 888, 777_000),
        ])),
    ]);

    $this->artisan('server-tracker:fetch-cloudflare-analytics', ['--date' => '2026-04-12'])->assertSuccessful();

    expect(CloudflareAnalytic::count())->toBe(1);
    expect(CloudflareAnalytic::first())
        ->unique_visitors->toBe(999)
        ->requests_total->toBe(888)
        ->bandwidth_total->toBe(777_000);
});

it('defaults to yesterday when no date option is provided', function () {
    makeCheckWithZone('zone-yesterday');

    Http::fake([
        'api.cloudflare.com/*' => Http::response(cloudflareAnalyticsResponse([
            analyticsZone('zone-yesterday', 10, 20, 30_000),
        ])),
    ]);

    $this->artisan('server-tracker:fetch-cloudflare-analytics')->assertSuccessful();

    expect(CloudflareAnalytic::first()->date->toDateString())->toBe(now()->subDay()->toDateString());
});

it('handles multiple zones in a single command run', function () {
    $check1 = makeCheckWithZone('zone-one');
    $check2 = makeCheckWithZone('zone-two');

    Http::fake([
        'api.cloudflare.com/*' => Http::response(cloudflareAnalyticsResponse([
            analyticsZone('zone-one', 100, 200, 300_000),
            analyticsZone('zone-two', 400, 500, 600_000),
        ])),
    ]);

    $this->artisan('server-tracker:fetch-cloudflare-analytics', ['--date' => '2026-04-12'])
        ->assertSuccessful();

    expect(CloudflareAnalytic::count())->toBe(2);
    expect(CloudflareAnalytic::where('monitor_cloudflare_check_id', $check1->id)->first()->unique_visitors)->toBe(100);
    expect(CloudflareAnalytic::where('monitor_cloudflare_check_id', $check2->id)->first()->unique_visitors)->toBe(400);
});

it('skips checks with no zone id', function () {
    Monitor::create([
        'url' => 'https://no-zone.com',
        'uptime_check_enabled' => true,
        'certificate_check_enabled' => false,
    ]);

    Http::fake();

    $this->artisan('server-tracker:fetch-cloudflare-analytics', ['--date' => '2026-04-12'])
        ->assertSuccessful();

    expect(CloudflareAnalytic::count())->toBe(0);
    Http::assertNothingSent();
});

it('logs a warning when a zone is queried but no data is returned', function () {
    $warnings = collect();

    Log::listen(function (MessageLogged $event) use ($warnings) {
        if ($event->level === 'warning') {
            $warnings->push($event);
        }
    });

    makeCheckWithZone('zone-missing');

    Http::fake([
        'api.cloudflare.com/*' => Http::response(cloudflareAnalyticsResponse([])),
    ]);

    $this->artisan('server-tracker:fetch-cloudflare-analytics', ['--date' => '2026-04-12'])
        ->assertSuccessful();

    $warning = $warnings->first();
    expect($warning->message)->toBe('No analytics returned for zone');
    expect($warning->context['zone_id'])->toBe('zone-missing');
    expect((string) $warning->context['monitor_url'])->toBe('https://example-zone-missing.com');
});

it('skips disabled cloudflare checks', function () {
    $monitor = Monitor::create([
        'url' => 'https://disabled-check.com',
        'uptime_check_enabled' => true,
        'certificate_check_enabled' => false,
    ]);

    $monitor->cloudflareCheck()->update([
        'enabled' => false,
        'cloudflare_zone_id' => 'zone-disabled',
    ]);

    Http::fake();

    $this->artisan('server-tracker:fetch-cloudflare-analytics', ['--date' => '2026-04-12'])
        ->assertSuccessful();

    expect(CloudflareAnalytic::count())->toBe(0);
    Http::assertNothingSent();
});
