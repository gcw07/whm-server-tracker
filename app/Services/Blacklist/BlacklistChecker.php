<?php

namespace App\Services\Blacklist;

use App\Enums\BlacklistStatusEnum;
use App\Models\Monitor;
use App\Models\MonitorBlacklistResult;
use App\Services\Blacklist\Contracts\BlacklistDriver;
use App\Services\Blacklist\Drivers\BarracudaCentralDriver;
use App\Services\Blacklist\Drivers\MultiSurblDriver;
use App\Services\Blacklist\Drivers\SpamCopDriver;
use App\Services\Blacklist\Drivers\SpamhausDBLDriver;
use App\Services\Blacklist\Drivers\SpamhausZenDriver;
use App\Services\Blacklist\Drivers\UrlhausDriver;
use Exception;
use Illuminate\Support\Facades\Cache;

class BlacklistChecker
{
    public function check(Monitor $monitor): void
    {
        $ttl = config('server-tracker.blacklist.cached_seconds', 7200);

        try {
            $domain = $monitor->url->getHost();
            $ip = $this->resolveIp($monitor, $domain, $ttl);

            // ALL ip-based results are cached by IP so domains sharing the same server
            // only trigger one set of remote lookups, but every domain gets its own rows.
            $ipResults = $ip
                ? Cache::remember("blacklist-dnsbl:{$ip}", $ttl, fn () => collect($this->ipBasedDrivers())
                    ->map(fn (BlacklistDriver $driver) => $driver->check($domain, $ip))
                    ->values()
                    ->all())
                : collect($this->ipBasedDrivers())
                    ->map(fn (BlacklistDriver $driver) => BlacklistResult::clean($driver->name()))
                    ->values()
                    ->all();

            // Domain-based checks always run per domain as they check the hostname itself.
            $domainResults = collect($this->domainBasedDrivers())
                ->map(fn (BlacklistDriver $driver) => $driver->check($domain, $ip));

            $allResults = collect($ipResults)->merge($domainResults);

            foreach ($ipResults as $result) {
                MonitorBlacklistResult::updateOrCreate(
                    ['monitor_id' => $monitor->id, 'driver' => $result->driver],
                    ['checked_value' => $ip, 'listed' => $result->listed, 'failure_reason' => $result->reason, 'checked_at' => now()],
                );
            }

            foreach ($domainResults as $result) {
                MonitorBlacklistResult::updateOrCreate(
                    ['monitor_id' => $monitor->id, 'driver' => $result->driver],
                    ['checked_value' => $domain, 'listed' => $result->listed, 'failure_reason' => $result->reason, 'checked_at' => now()],
                );
            }

            $overallStatus = $allResults->contains(fn (BlacklistResult $result) => $result->listed)
                ? BlacklistStatusEnum::Invalid
                : BlacklistStatusEnum::Valid;

            $monitor->blacklistCheck->update(['status' => $overallStatus]);
        } catch (Exception) {
            $monitor->blacklistCheck->update(['status' => BlacklistStatusEnum::Invalid]);
        }
    }

    public static function driverNames(): array
    {
        return collect((new self)->ipBasedDrivers())
            ->merge((new self)->domainBasedDrivers())
            ->map(fn (BlacklistDriver $driver) => $driver->name())
            ->all();
    }

    protected function resolveIp(Monitor $monitor, string $domain, int $ttl): ?string
    {
        // Prefer the known server IP — DNS may return a Cloudflare proxy IP.
        // Order by suspended ascending so non-suspended accounts (0) come before suspended ones (1),
        // handling the common case where an account migrated to a new server and the old one is suspended.
        $serverIp = $monitor->accounts()->with('server')->orderBy('suspended')->first()?->server?->address;

        if ($serverIp) {
            return $serverIp;
        }

        // Fall back to DNS resolution for monitors not linked to a server.
        return Cache::remember("blacklist-ip:{$domain}", $ttl, function () use ($domain) {
            $ip = gethostbyname($domain);

            // gethostbyname returns the input unchanged when resolution fails
            return $ip !== $domain ? $ip : null;
        });
    }

    /** @return BlacklistDriver[] */
    protected function ipBasedDrivers(): array
    {
        return [
            new BarracudaCentralDriver,
            new SpamCopDriver,
            new SpamhausZenDriver,
        ];
    }

    /** @return BlacklistDriver[] */
    protected function domainBasedDrivers(): array
    {
        return [
            new SpamhausDBLDriver,
            new MultiSurblDriver,
            new UrlhausDriver,
        ];
    }
}
