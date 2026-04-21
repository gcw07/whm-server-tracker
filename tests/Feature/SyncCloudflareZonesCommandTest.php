<?php

use App\Models\Monitor;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(LazilyRefreshDatabase::class);

function cloudflareZonesResponse(array $zones): array
{
    return [
        'result' => collect($zones)->map(fn ($zone) => [
            'id' => $zone['id'],
            'name' => $zone['name'],
            'status' => $zone['status'],
            'account' => ['id' => $zone['account_id'] ?? 'default-account-id'],
        ])->values()->all(),
        'result_info' => [
            'page' => 1,
            'per_page' => 1000,
            'total_count' => count($zones),
        ],
        'success' => true,
    ];
}

function makeMonitorOnCloudflare(string $domain): Monitor
{
    $monitor = Monitor::create([
        'url' => "https://{$domain}",
        'uptime_check_enabled' => true,
        'certificate_check_enabled' => false,
    ]);

    $monitor->domainCheck()->update(['is_on_cloudflare' => true]);

    return $monitor;
}

it('syncs zone id, account id, and status for monitors on cloudflare', function () {
    $monitor = makeMonitorOnCloudflare('example.com');

    Http::fake([
        'api.cloudflare.com/*' => Http::response(cloudflareZonesResponse([
            ['id' => 'abc123', 'name' => 'example.com', 'status' => 'active', 'account_id' => 'acct001'],
        ])),
    ]);

    $this->artisan('server-tracker:sync-cloudflare-zones')->assertSuccessful();

    expect($monitor->cloudflareCheck->fresh())
        ->cloudflare_zone_id->toBe('abc123')
        ->cloudflare_account_id->toBe('acct001')
        ->zone_status->toBe('active')
        ->last_synced_at->not->toBeNull();
});

it('skips monitors whose domain is not in the cloudflare account', function () {
    $monitor = makeMonitorOnCloudflare('client-site.com');

    Http::fake([
        'api.cloudflare.com/*' => Http::response(cloudflareZonesResponse([
            ['id' => 'xyz789', 'name' => 'my-site.com', 'status' => 'active'],
        ])),
    ]);

    $this->artisan('server-tracker:sync-cloudflare-zones')->assertSuccessful();

    expect($monitor->cloudflareCheck->fresh())
        ->cloudflare_zone_id->toBeNull()
        ->last_synced_at->toBeNull();
});

it('skips monitors where cloudflare check is disabled', function () {
    $monitor = Monitor::create([
        'url' => 'https://example.com',
        'uptime_check_enabled' => true,
        'certificate_check_enabled' => false,
    ]);

    $monitor->domainCheck()->update(['is_on_cloudflare' => true]);
    $monitor->cloudflareCheck()->update(['enabled' => false]);

    Http::fake([
        'api.cloudflare.com/*' => Http::response(cloudflareZonesResponse([
            ['id' => 'abc123', 'name' => 'example.com', 'status' => 'active'],
        ])),
    ]);

    $this->artisan('server-tracker:sync-cloudflare-zones')->assertSuccessful();

    expect($monitor->cloudflareCheck->fresh())
        ->cloudflare_zone_id->toBeNull();
});

it('skips monitors where is_on_cloudflare is false', function () {
    $monitor = Monitor::create([
        'url' => 'https://example.com',
        'uptime_check_enabled' => true,
        'certificate_check_enabled' => false,
    ]);

    // is_on_cloudflare defaults to false; no update needed

    Http::fake([
        'api.cloudflare.com/*' => Http::response(cloudflareZonesResponse([
            ['id' => 'abc123', 'name' => 'example.com', 'status' => 'active'],
        ])),
    ]);

    $this->artisan('server-tracker:sync-cloudflare-zones')->assertSuccessful();

    expect($monitor->cloudflareCheck->fresh())->cloudflare_zone_id->toBeNull();
});

it('syncs multiple monitors in a single api call', function () {
    $monitor1 = makeMonitorOnCloudflare('site-one.com');
    $monitor2 = makeMonitorOnCloudflare('site-two.com');

    Http::fake([
        'api.cloudflare.com/*' => Http::response(cloudflareZonesResponse([
            ['id' => 'aaa111', 'name' => 'site-one.com', 'status' => 'active'],
            ['id' => 'bbb222', 'name' => 'site-two.com', 'status' => 'active'],
        ])),
    ]);

    $this->artisan('server-tracker:sync-cloudflare-zones')->assertSuccessful();

    expect($monitor1->cloudflareCheck->fresh()->cloudflare_zone_id)->toBe('aaa111');
    expect($monitor2->cloudflareCheck->fresh()->cloudflare_zone_id)->toBe('bbb222');

    Http::assertSentCount(1);
});
