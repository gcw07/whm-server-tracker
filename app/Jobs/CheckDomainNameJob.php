<?php

namespace App\Jobs;

use App\Models\Monitor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CheckDomainNameJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Monitor $monitor;

    public int $tries = 0;

    public int $maxExceptions = 3;

    public function __construct(Monitor $monitor)
    {
        $this->monitor = $monitor;
    }

    public function handle(): void
    {
        $cacheKey = "rdap-api-limit-{$this->monitor->id}";

        if ($timestamp = Cache::get($cacheKey)) {
            $this->release(
                $timestamp - time()
            );

            return;
        }

        $rdapServer = config('server-tracker.rdap_server');

        $response = Http::acceptJson()
            ->timeout(20)
            ->get("https://$rdapServer/domain/{$this->monitor->url->getHost()}");

        if ($response->failed() && $response->status() == 429) {
            $secondsRemaining = $response->header('Retry-After');

            Cache::put(
                $cacheKey,
                now()->addSeconds($secondsRemaining)->timestamp,
                $secondsRemaining
            );

            $this->release(
                $secondsRemaining
            );

            return;
        }

        $this->monitor->processDomainNameExpiration($response);
    }

    public function retryUntil()
    {
        return now()->addHours(12);
    }
}
