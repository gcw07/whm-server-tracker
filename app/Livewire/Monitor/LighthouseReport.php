<?php

namespace App\Livewire\Monitor;

use App\Models\LighthouseAudit;
use App\Models\Monitor;
use Livewire\Component;

class LighthouseReport extends Component
{
    public Monitor $monitor;

    public $audits;

    public $selectedAudit;

    public function mount(Monitor $monitor)
    {
        $this->monitor = $monitor;
        $this->audits = $this->monitor->lighthouseLatestAudit()->get();
        $this->selectedAudit = $this->audits->first();
    }

    public function render()
    {
        return view('livewire.monitor.reports', [])->layoutData(['title' => 'Monitors']);
    }

    public function changeSelected(LighthouseAudit $audit)
    {
        $this->selectedAudit = $audit;
    }
}
