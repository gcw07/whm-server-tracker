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
        return view('livewire.monitor.reports', [
            'monitor' => $this->monitor,
        ])->layoutData(['title' => 'Monitors']);
    }
}
