<?php

use App\Models\Account;
use App\Models\LighthouseAudit;
use App\Models\Monitor;
use Illuminate\Support\Facades\Artisan;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Monitor Details')] class extends Component
{
    public Monitor $monitor;

    public function mount(Monitor $monitor): void
    {
        $this->monitor = $monitor;
        $this->monitor->loadMissing(['accounts', 'accounts.server', 'accounts.sslCertificates', 'blacklistCheck', 'lighthouseCheck', 'domainCheck']);
    }

    #[Computed]
    public function sslCertificates(): \Illuminate\Support\Collection
    {
        return $this->monitor->accounts
            ->flatMap(fn (Account $account) => $account->sslCertificates)
            ->sortBy('servername')
            ->values();
    }

    #[Computed]
    public function lighthouseStats(): LighthouseAudit|\Illuminate\Database\Eloquent\Builder|null
    {
        return LighthouseAudit::query()
            ->where('monitor_id', $this->monitor->id)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    public function enableAllMonitors(): void
    {
        $this->monitor->uptime_check_enabled = true;
        $this->monitor->save();

        $this->monitor->blacklistCheck->update(['enabled' => true]);
        $this->monitor->lighthouseCheck->update(['enabled' => true]);
        $this->monitor->domainCheck->update(['enabled' => true]);

        Flux::toast(
            text: 'Enabled all monitors for this URL.',
            heading: 'Enabling checks...',
            variant: 'success',
        );
    }

    public function disableAllMonitors(): void
    {
        $this->monitor->uptime_check_enabled = false;
        $this->monitor->save();

        $this->monitor->blacklistCheck->update(['enabled' => false]);
        $this->monitor->lighthouseCheck->update(['enabled' => false]);
        $this->monitor->domainCheck->update(['enabled' => false]);

        Flux::toast(
            text: 'Disable all monitors for this URL.',
            heading: 'Disabling checks...',
            variant: 'success',
        );
    }

    public function refreshCertificateCheck(): void
    {
        Artisan::call('monitor:check-certificate', [
            '--url' => $this->monitor->url,
        ]);

        Flux::toast(
            text: 'The SSL certificate for this URL will be checked shortly.',
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

    public function toggleUptimeCheck(): void
    {
        if ($this->monitor->uptime_check_enabled) {
            $this->monitor->uptime_check_enabled = false;
        } else {
            $this->monitor->uptime_check_enabled = true;
        }

        $this->monitor->save();
    }

    public function toggleBlacklistCheck(): void
    {
        $this->monitor->blacklistCheck->update([
            'enabled' => ! $this->monitor->blacklistCheck->enabled,
        ]);
    }

    public function toggleLighthouseCheck(): void
    {
        $this->monitor->lighthouseCheck->update([
            'enabled' => ! $this->monitor->lighthouseCheck->enabled,
        ]);
    }

    public function toggleDomainNameExpirationCheck(): void
    {
        $this->monitor->domainCheck->update([
            'enabled' => ! $this->monitor->domainCheck->enabled,
        ]);
    }
};
