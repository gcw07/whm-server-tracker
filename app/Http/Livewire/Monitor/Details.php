<?php

namespace App\Http\Livewire\Monitor;

use App\Models\Account;
use App\Models\LighthouseAudit;
use App\Models\Monitor;
use Illuminate\Support\Facades\Artisan;
use Livewire\Component;
use Usernotnull\Toast\Concerns\WireToast;

class Details extends Component
{
    use WireToast;

    public Monitor $monitor;

    public string $domainUrl;

    public int $accountsCount;

    public function mount(Monitor $monitor)
    {
        $this->monitor = $monitor;
    }

    public function render()
    {
        $this->domainUrl = preg_replace('(^https?://)', '', $this->monitor->url);
        $this->accountsCount = $this->accountCountQuery();

        return view('livewire.monitor.details', [
            'account' => $this->accountQuery(),
            'lighthouseStats' => $this->lighthouseQuery(),
            'uptimeForToday' => $this->monitor->uptime_for_today,
            'uptimeForLastSevenDays' => $this->monitor->uptime_for_last_seven_days,
            'uptimeForLastThirtyDays' => $this->monitor->uptime_for_last_thirty_days,
        ])->layoutData(['title' => 'Monitor Details']);
    }

    protected function accountCountQuery(): int
    {
        return Account::query()
            ->where('domain', $this->domainUrl)
            ->count();
    }

    protected function accountQuery()
    {
        return Account::query()
            ->with(['server'])
            ->where('domain', $this->domainUrl)
            ->first();
    }

    protected function lighthouseQuery()
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

        toast()->success('Enabled all monitors for this URL.')->push();
    }

    public function disableAllMonitors(): void
    {
        $this->monitor->uptime_check_enabled = false;
        $this->monitor->certificate_check_enabled = false;
        $this->monitor->blacklist_check_enabled = false;
        $this->monitor->lighthouse_check_enabled = false;
        $this->monitor->domain_name_check_enabled = false;
        $this->monitor->save();

        toast()->success('Disabled all monitors for this URL.')->push();
    }

    public function toggleUptimeCheck(): void
    {
        if ($this->monitor->uptime_check_enabled) {
            $this->monitor->uptime_check_enabled = false;
            $this->monitor->save();

            toast()->success('Turned OFF uptime checking for this URL.')->push();
        } else {
            $this->monitor->uptime_check_enabled = true;
            $this->monitor->save();

            toast()->success('Turned ON uptime checking for this URL.')->push();
        }
    }

    public function toggleCertificateCheck(): void
    {
        if ($this->monitor->certificate_check_enabled) {
            $this->monitor->certificate_check_enabled = false;
            $this->monitor->save();

            toast()->success('Turned OFF ssl certificate checking for this URL.')->push();
        } else {
            $this->monitor->certificate_check_enabled = true;
            $this->monitor->save();

            toast()->success('Turned ON ssl certificate checking for this URL.')->push();
        }
    }

    public function toggleBlacklistCheck(): void
    {
        if ($this->monitor->blacklist_check_enabled) {
            $this->monitor->blacklist_check_enabled = false;
            $this->monitor->save();

            toast()->success('Turned OFF blacklist checking for this URL.')->push();
        } else {
            $this->monitor->blacklist_check_enabled = true;
            $this->monitor->save();

            toast()->success('Turned ON blacklist checking for this URL.')->push();
        }
    }

    public function toggleLighthouseCheck(): void
    {
        if ($this->monitor->lighthouse_check_enabled) {
            $this->monitor->lighthouse_check_enabled = false;
            $this->monitor->save();

            toast()->success('Turned OFF lighthouse reports for this URL.')->push();
        } else {
            $this->monitor->lighthouse_check_enabled = true;
            $this->monitor->save();

            toast()->success('Turned ON lighthouse reports for this URL.')->push();
        }
    }

    public function toggleDomainNameExpirationCheck(): void
    {
        if ($this->monitor->domain_name_check_enabled) {
            $this->monitor->domain_name_check_enabled = false;
            $this->monitor->save();

            toast()->success('Turned OFF domain name expiration checking for this URL.')->push();
        } else {
            $this->monitor->domain_name_check_enabled = true;
            $this->monitor->save();

            toast()->success('Turned ON domain name expiration checking for this URL.')->push();
        }
    }

    public function refreshCertificateCheck(): void
    {
        Artisan::call('monitor:check-certificate', [
            '--url' => $this->monitor->url,
        ]);

        toast()->success('The SSL certificate for this URL will be checked shortly.')->push();
    }

    public function refreshBlacklistCheck(): void
    {
        Artisan::call('server-tracker:check-blacklist', [
            '--url' => $this->monitor->url,
        ]);

        toast()->success('The email blacklist for this URL will be checked shortly.')->push();
    }

    public function refreshDomainInfoCheck(): void
    {
        Artisan::call('server-tracker:check-domain-name', [
            '--url' => $this->monitor->url,
        ]);

        toast()->success('The domain name expiration and nameservers for this URL will be checked shortly.')->push();
    }

    public function refreshLighthouseCheck(): void
    {
        Artisan::call('server-tracker:check-lighthouse', [
            '--url' => $this->monitor->url,
        ]);

        toast()->success('The lighthouse report for this URL will be checked shortly.')->push();
    }
}
