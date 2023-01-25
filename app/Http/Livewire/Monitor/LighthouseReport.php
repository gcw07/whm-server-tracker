<?php

namespace App\Http\Livewire\Monitor;

use App\Models\Monitor;
use Livewire\Component;

class LighthouseReport extends Component
{
    public Monitor $monitor;

    protected string $domainUrl;

    public function mount(Monitor $monitor)
    {
        $this->monitor = $monitor;
    }

    public function render()
    {
        $audit = $this->monitor->lighthouseLatestAudit()->first();

        return view('livewire.monitor.reports', [
            'audit' => $audit,
            'monitor' => $this->monitor,
        ])->layoutData(['title' => 'Monitors']);
    }
}
