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

    protected string $domainUrl;

    public function mount(Monitor $monitor)
    {
        $this->monitor = $monitor;
    }

    public function render()
    {
        $this->domainUrl = preg_replace('(^https?://)', '', $this->monitor->url);

        return view('livewire.monitor.details', [
            'accounts' => $this->accountQuery(),
            'lighthouseStats' => $this->lighthouseQuery(),
            'uptimeForToday' => $this->monitor->uptime_for_today,
            'uptimeForLastSevenDays' => $this->monitor->uptime_for_last_seven_days,
            'uptimeForLastThirtyDays' => $this->monitor->uptime_for_last_thirty_days,
        ])->layoutData(['title' => 'Monitor Details']);
    }

    protected function accountQuery()
    {
        return Account::query()
            ->with(['server'])
            ->where('domain', $this->domainUrl)
            ->get();
    }

    protected function lighthouseQuery()
    {
        return LighthouseAudit::query()
            ->where('monitor_id', $this->monitor->id)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    public function toggleUptimeCheck()
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

    public function toggleCertificateCheck()
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

    public function toggleBlacklistCheck()
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

    public function toggleLighthouseCheck()
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

    public function toggleDomainNameExpirationCheck()
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

    public function refreshCertificateCheck()
    {
        Artisan::call('monitor:check-certificate', [
            '--url' => $this->monitor->url,
        ]);

        toast()->success('The SSL certificate for this URL will be checked shortly.')->push();
    }
}
