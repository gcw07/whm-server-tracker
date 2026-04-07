<?php

namespace App\Services\Blacklist\Drivers;

use App\Services\Blacklist\BlacklistResult;
use App\Services\Blacklist\Contracts\BlacklistDriver;
use Illuminate\Support\Facades\Http;

class UrlhausDriver implements BlacklistDriver
{
    public function name(): string
    {
        return 'URLhaus';
    }

    public function url(): string
    {
        return 'https://urlhaus.abuse.ch/';
    }

    public function check(string $domain, ?string $ip): BlacklistResult
    {
        $response = Http::asForm()
            ->timeout(10)
            ->post('https://urlhaus-api.abuse.ch/v1/host/', ['host' => $domain]);

        if ($response->failed()) {
            return BlacklistResult::clean($this->name());
        }

        if ($response->json('query_status') === 'is_host') {
            return BlacklistResult::listed($this->name(), 'Listed on URLhaus (abuse.ch)');
        }

        return BlacklistResult::clean($this->name());
    }
}
