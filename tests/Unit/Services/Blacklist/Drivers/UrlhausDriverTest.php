<?php

use App\Services\Blacklist\Drivers\UrlhausDriver;
use Illuminate\Support\Facades\Http;

test('urlhaus driver returns listed result when query_status is is_host', function () {
    Http::fake([
        'urlhaus-api.abuse.ch/*' => Http::response(['query_status' => 'is_host'], 200),
    ]);

    $result = (new UrlhausDriver)->check('example.com', null);

    expect($result->listed)->toBeTrue();
    expect($result->driver)->toBe('URLhaus');
    expect($result->reason)->toBe('Listed on URLhaus (abuse.ch)');
});

test('urlhaus driver returns clean result when query_status is no_results', function () {
    Http::fake([
        'urlhaus-api.abuse.ch/*' => Http::response(['query_status' => 'no_results'], 200),
    ]);

    $result = (new UrlhausDriver)->check('example.com', null);

    expect($result->listed)->toBeFalse();
});

test('urlhaus driver returns clean result when http request fails', function () {
    Http::fake([
        'urlhaus-api.abuse.ch/*' => Http::response(null, 500),
    ]);

    $result = (new UrlhausDriver)->check('example.com', null);

    expect($result->listed)->toBeFalse();
});
