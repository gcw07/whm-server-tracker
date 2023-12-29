<?php

namespace App\Livewire\Monitor;

use App\Models\LighthouseAudit;
use Livewire\Component;
use Usernotnull\Toast\Concerns\WireToast;

class LighthouseFrame extends Component
{
    use WireToast;

    public LighthouseAudit $audit;

    protected string $domainUrl;

    public function mount(LighthouseAudit $audit)
    {
        $this->audit = $audit;
    }

    public function render()
    {
        return view('livewire.monitor.lighthouse-iframe', [
            'audit' => $this->audit,
        ])->layout('components.layouts.empty');
    }
}
