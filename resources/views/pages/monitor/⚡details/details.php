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
    public int $monitorId;

    public function mount(int $monitor): void
    {
        $this->monitorId = $monitor;
    }

    #[Computed]
    public function monitor(): \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|Monitor|null
    {
        return Monitor::with([
            'accounts',
            'accounts.server',
            'accounts.sslCertificates',
            'blacklistCheck',
            'lighthouseCheck',
            'domainCheck',
            'wordpressCheck',
        ])->findOrFail($this->monitorId);
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
            ->where('monitor_id', $this->monitorId)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    public function enableAllMonitors(): void
    {
        $monitor = $this->monitor;
        $monitor->uptime_check_enabled = true;
        $monitor->certificate_check_enabled = true;
        $monitor->save();

        $monitor->blacklistCheck->update(['enabled' => true]);
        $monitor->lighthouseCheck->update(['enabled' => true]);
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
        $monitor->lighthouseCheck->update(['enabled' => false]);
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

        $monitor->lighthouseCheck->update([
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
