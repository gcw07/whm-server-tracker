<?php

use App\Jobs\CheckDomainNameJob;
use App\Models\Monitor;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

uses(LazilyRefreshDatabase::class);

it('caches the retry timestamp when the RDAP API returns a 429 with a Retry-After header', function () {
    $monitor = Monitor::create([
        'url' => 'https://example.com',
        'uptime_check_enabled' => true,
        'certificate_check_enabled' => false,
    ]);

    Http::fake([
        '*' => Http::response(null, 429, ['Retry-After' => '60']),
    ]);

    CheckDomainNameJob::dispatchSync($monitor);

    $cacheKey = "rdap-api-limit-{$monitor->id}";

    expect(Cache::has($cacheKey))->toBeTrue();

    $cachedTimestamp = Cache::get($cacheKey);
    expect($cachedTimestamp)->toBeInt();
    expect($cachedTimestamp)->toBeGreaterThan(time());
});

it('does not throw a TypeError when the Retry-After header value is a string', function () {
    $monitor = Monitor::create([
        'url' => 'https://example.com',
        'uptime_check_enabled' => true,
        'certificate_check_enabled' => false,
    ]);

    Http::fake([
        '*' => Http::response(null, 429, ['Retry-After' => '30']),
    ]);

    expect(fn () => CheckDomainNameJob::dispatchSync($monitor))->not->toThrow(TypeError::class);
});
