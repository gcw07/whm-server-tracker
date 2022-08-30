<?php

namespace App\Http\Livewire\Monitor;

use App\Models\Account;
use Livewire\Component;
use Spatie\UptimeMonitor\Models\Monitor;
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
        ])->layoutData(['title' => 'Monitor Details']);
    }

    protected function accountQuery()
    {
        return Account::query()
            ->with(['server'])
            ->where('domain', $this->domainUrl)
            ->get();
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
}
