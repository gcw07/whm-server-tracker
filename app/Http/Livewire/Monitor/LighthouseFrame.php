<?php

namespace App\Http\Livewire\Monitor;

use App\Models\Account;
use App\Models\Monitor;
use Livewire\Component;
use Usernotnull\Toast\Concerns\WireToast;

class LighthouseFrame extends Component
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
        $audit = $this->monitor->lighthouseAudits()->first();

        return view('livewire.monitor.lighthouse-iframe', [
            'audit' => $audit,
        ])->layout('components.layouts.empty');
    }

    protected function accountQuery()
    {
        return Account::query()
            ->with(['server'])
            ->where('domain', $this->domainUrl)
            ->get();
    }
}
