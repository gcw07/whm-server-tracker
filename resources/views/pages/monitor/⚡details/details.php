<?php

use App\Models\Account;
use App\Models\LighthouseAudit;
use App\Models\Monitor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Monitor Details')] class extends Component
{
    public int $monitorId;

    public string $uptimePeriod = '30';

    public string $lighthouseFormFactor = 'desktop';

    public string $cloudflareAnalyticsPeriod = '7';

    public function mount(int $monitor): void
    {
        $this->monitorId = $monitor;
    }

    #[Computed]
    public function monitor(): Model|Illuminate\Database\Eloquent\Collection|Monitor|null
    {
        return Monitor::with([
            'accounts',
            'accounts.server',
            'accounts.sslCertificates',
            'blacklistCheck.results',
            'lighthouseChecks',
            'lighthouseCheck',
            'domainCheck',
            'wordpressCheck',
            'cloudflareCheck',
        ])->findOrFail($this->monitorId);
    }

    #[Computed]
    public function sslCertificates(): Collection
    {
        return $this->monitor->accounts
            ->flatMap(fn (Account $account) => $account->sslCertificates)
            ->sortBy('servername')
            ->values();
    }

    #[Computed]
    public function uptimeChartData(): array
    {
        $days = (int) $this->uptimePeriod;

        return collect(range($days - 1, 0))
            ->map(function (int $daysAgo): array {
                $date = now()->subDays($daysAgo);

                $uptime = round($this->monitor->calculateUptime($date, $date), 1);

                return [
                    'date'     => $date->format('M j'),
                    'uptime'   => $uptime,
                    'downtime' => round(100 - $uptime, 1),
                ];
            })
            ->values()
            ->toArray();
    }

    #[Computed]
    public function cloudflareChartData(): array
    {
        $days = (int) $this->cloudflareAnalyticsPeriod;
        $cloudflareCheck = $this->monitor->cloudflareCheck;

        if (! $cloudflareCheck || ! $cloudflareCheck->cloudflare_zone_id) {
            return [];
        }

        $analytics = $cloudflareCheck->analytics()
            ->where('date', '>=', now()->subDays($days)->startOfDay())
            ->where('date', '<', now()->startOfDay())
            ->orderBy('date')
            ->get()
            ->keyBy(fn ($a) => $a->date->format('Y-m-d'));

        return collect(range($days, 1))
            ->map(function (int $daysAgo) use ($analytics): array {
                $date = now()->subDays($daysAgo);
                $analytic = $analytics->get($date->format('Y-m-d'));

                return [
                    'date'            => $date->format('M j'),
                    'unique_visitors' => $analytic?->unique_visitors ?? 0,
                    'requests_total'  => $analytic?->requests_total ?? 0,
                    'bandwidth_mb'    => $analytic?->bandwidth_total
                        ? round($analytic->bandwidth_total / 1024 / 1024, 1)
                        : 0,
                ];
            })
            ->values()
            ->toArray();
    }

    #[Computed]
    public function cloudflareAnalyticsTotals(): array
    {
        $days = (int) $this->cloudflareAnalyticsPeriod;
        $cloudflareCheck = $this->monitor->cloudflareCheck;

        if (! $cloudflareCheck || ! $cloudflareCheck->cloudflare_zone_id) {
            return [];
        }

        $current = $cloudflareCheck->analytics()
            ->where('date', '>=', now()->subDays($days)->startOfDay())
            ->where('date', '<', now()->startOfDay())
            ->selectRaw('SUM(unique_visitors) as total_visitors, SUM(requests_total) as total_requests, SUM(bandwidth_total) as total_bandwidth')
            ->first();

        $prior = $cloudflareCheck->analytics()
            ->where('date', '>=', now()->subDays($days * 2)->startOfDay())
            ->where('date', '<', now()->subDays($days)->startOfDay())
            ->selectRaw('SUM(unique_visitors) as total_visitors, SUM(requests_total) as total_requests, SUM(bandwidth_total) as total_bandwidth')
            ->first();

        $bandwidthBytes = (int) ($current->total_bandwidth ?? 0);

        return [
            'unique_visitors'        => $this->formatCompact((int) ($current->total_visitors ?? 0)),
            'requests_total'         => $this->formatCompact((int) ($current->total_requests ?? 0)),
            'bandwidth'              => $bandwidthBytes >= 1_073_741_824
                ? number_format($bandwidthBytes / 1_073_741_824, 2) . ' GB'
                : number_format($bandwidthBytes / 1_048_576, 1) . ' MB',
            'unique_visitors_change' => $this->formatPercentageChange(
                (int) ($current->total_visitors ?? 0),
                (int) ($prior->total_visitors ?? 0),
            ),
            'requests_total_change'  => $this->formatPercentageChange(
                (int) ($current->total_requests ?? 0),
                (int) ($prior->total_requests ?? 0),
            ),
            'bandwidth_change'       => $this->formatPercentageChange(
                (int) ($current->total_bandwidth ?? 0),
                (int) ($prior->total_bandwidth ?? 0),
            ),
        ];
    }

    /**
     * @return array{value: string, direction: 'up'|'down'|'flat'}
     */
    private function formatPercentageChange(int $current, int $prior): array
    {
        if ($prior === 0) {
            return ['value' => '—', 'direction' => 'flat'];
        }

        $pct = (($current - $prior) / $prior) * 100;

        if (abs($pct) < 0.5) {
            return ['value' => 'No change', 'direction' => 'flat'];
        }

        return [
            'value'     => ($pct > 0 ? '+' : '') . number_format($pct, 1) . '%',
            'direction' => $pct > 0 ? 'up' : 'down',
        ];
    }

    private function formatCompact(int $n): string
    {
        if ($n >= 1_000_000) {
            return rtrim(rtrim(number_format($n / 1_000_000, 1), '0'), '.') . 'M';
        }

        if ($n >= 1_000) {
            return rtrim(rtrim(number_format($n / 1_000, 1), '0'), '.') . 'k';
        }

        return (string) $n;
    }

    public function updatedCloudflareAnalyticsPeriod(): void
    {
        unset($this->cloudflareChartData);
        unset($this->cloudflareAnalyticsTotals);
    }

    #[Computed]
    public function lighthouseStats(): LighthouseAudit|Builder|null
    {
        return LighthouseAudit::query()
            ->where('monitor_id', $this->monitorId)
            ->where('form_factor', $this->lighthouseFormFactor)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    public function switchLighthouseFormFactor(string $formFactor): void
    {
        $this->lighthouseFormFactor = $formFactor;
        unset($this->lighthouseStats);
    }

    public function enableAllMonitors(): void
    {
        $monitor = $this->monitor;
        $monitor->uptime_check_enabled = true;

        if ($monitor->uptime_status === UptimeStatus::DOWN) {
            $monitor->resetUptimeStatus();
        }

        $monitor->certificate_check_enabled = true;
        $monitor->save();

        $monitor->blacklistCheck->update(['enabled' => true]);
        $monitor->lighthouseChecks()->update(['enabled' => true]);
        $monitor->domainCheck->update(['enabled' => true]);
        $monitor->wordpressCheck->update(['enabled' => true]);

        unset($this->monitor);

        Flux::toast(
            text: 'Enabled all monitors for this URL.',
            heading: 'Enabling checks...',
            variant: 'success',
        );
    }

    public function disableAllMonitors(): void
    {
        $monitor = $this->monitor;

        $monitor->uptime_check_enabled = false;
        $monitor->certificate_check_enabled = false;
        $monitor->save();

        $monitor->blacklistCheck->update(['enabled' => false]);
        $monitor->lighthouseChecks()->update(['enabled' => false]);
        $monitor->domainCheck->update(['enabled' => false]);
        $monitor->wordpressCheck->update(['enabled' => false]);

        unset($this->monitor);

        Flux::toast(
            text: 'Disable all monitors for this URL.',
            heading: 'Disabling checks...',
            variant: 'success',
        );
    }

    public function refreshWordPressCheck(): void
    {
        Artisan::call('server-tracker:check-wordpress', [
            '--url' => $this->monitor->url,
        ]);

        Flux::toast(
            text: 'The WordPress check for this URL will run shortly.',
            heading: 'Checking...',
            variant: 'success',
        );
    }

    public function refreshBlacklistCheck(): void
    {
        Artisan::call('server-tracker:check-blacklist', [
            '--url' => $this->monitor->url,
        ]);

        Flux::toast(
            text: 'The email blacklist for this URL will be checked shortly.',
            heading: 'Checking...',
            variant: 'success',
        );
    }

    public function refreshDomainInfoCheck(): void
    {
        Artisan::call('server-tracker:check-domain-name', [
            '--url' => $this->monitor->url,
        ]);

        Flux::toast(
            text: 'The domain name expiration and nameservers for this URL will be checked shortly.',
            heading: 'Checking...',
            variant: 'success',
        );
    }

    public function refreshLighthouseCheck(): void
    {
        Artisan::call('server-tracker:check-lighthouse', [
            '--url' => $this->monitor->url,
        ]);

        Flux::toast(
            text: 'The lighthouse report for this URL will be checked shortly.',
            heading: 'Checking...',
            variant: 'success',
        );
    }

    public function toggleCertificateCheck(): void
    {
        $monitor = $this->monitor;

        $monitor->certificate_check_enabled = ! $monitor->certificate_check_enabled;

        $monitor->save();

        unset($this->monitor);
    }

    public function toggleUptimeCheck(): void
    {
        $monitor = $this->monitor;

        if ($monitor->uptime_check_enabled) {
            $monitor->uptime_check_enabled = false;
        } else {
            $monitor->uptime_check_enabled = true;

            if ($monitor->uptime_status === UptimeStatus::DOWN) {
                $monitor->resetUptimeStatus();
            }
        }

        $monitor->save();

        unset($this->monitor);
    }

    public function toggleBlacklistCheck(): void
    {
        $monitor = $this->monitor;

        $monitor->blacklistCheck->update([
            'enabled' => ! $monitor->blacklistCheck->enabled,
        ]);

        unset($this->monitor);
    }

    public function toggleLighthouseCheck(): void
    {
        $monitor = $this->monitor;

        $monitor->lighthouseChecks()->update([
            'enabled' => ! $monitor->lighthouseCheck->enabled,
        ]);

        unset($this->monitor);
    }

    public function toggleDomainNameExpirationCheck(): void
    {
        $monitor = $this->monitor;

        $monitor->domainCheck->update([
            'enabled' => ! $monitor->domainCheck->enabled,
        ]);

        unset($this->monitor);
    }

    public function toggleWordPressCheck(): void
    {
        $monitor = $this->monitor;

        $monitor->wordpressCheck->update([
            'enabled' => ! $monitor->wordpressCheck->enabled,
        ]);

        unset($this->monitor);
    }
};
