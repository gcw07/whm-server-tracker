<?php

namespace App\Jobs;

use App\Models\Monitor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\Attributes\MaxExceptions;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

#[Tries(0)]
#[MaxExceptions(3)]
class CheckDomainNameJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Monitor $monitor;

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
            $secondsRemaining = (int) $response->header('Retry-After');

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
