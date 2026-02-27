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
        $this->monitor->loadMissing(['accounts', 'accounts.server']);
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
        $this->monitor->certificate_check_enabled = true;
        $this->monitor->blacklist_check_enabled = true;
        $this->monitor->lighthouse_check_enabled = true;
        $this->monitor->domain_name_check_enabled = true;
        $this->monitor->save();

        Flux::toast(
            text: 'Enabled all monitors for this URL.',
            heading: 'Enabling checks...',
            variant: 'success',
        );
    }

    public function disableAllMonitors(): void
    {
        $this->monitor->uptime_check_enabled = false;
        $this->monitor->certificate_check_enabled = false;
        $this->monitor->blacklist_check_enabled = false;
        $this->monitor->lighthouse_check_enabled = false;
        $this->monitor->domain_name_check_enabled = false;
        $this->monitor->save();

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

    public function toggleCertificateCheck(): void
    {
        if ($this->monitor->certificate_check_enabled) {
            $this->monitor->certificate_check_enabled = false;
        } else {
            $this->monitor->certificate_check_enabled = true;
        }

        $this->monitor->save();
    }

    public function toggleBlacklistCheck(): void
    {
        if ($this->monitor->blacklist_check_enabled) {
            $this->monitor->blacklist_check_enabled = false;
        } else {
            $this->monitor->blacklist_check_enabled = true;
        }

        $this->monitor->save();
    }

    public function toggleLighthouseCheck(): void
    {
        if ($this->monitor->lighthouse_check_enabled) {
            $this->monitor->lighthouse_check_enabled = false;
        } else {
            $this->monitor->lighthouse_check_enabled = true;
        }

        $this->monitor->save();
    }

    public function toggleDomainNameExpirationCheck(): void
    {
        if ($this->monitor->domain_name_check_enabled) {
            $this->monitor->domain_name_check_enabled = false;
        } else {
            $this->monitor->domain_name_check_enabled = true;
        }

        $this->monitor->save();
    }
};
