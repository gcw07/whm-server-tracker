<?php

use App\Models\LighthouseAudit;
use App\Models\Monitor;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Lighthouse Report')] class extends Component
{
    public Monitor $monitor;

    public string $domainUrl;

    public $audits;

    public $selectedAudit;

    public function mount(Monitor $monitor): void
    {
        $this->monitor = $monitor;
        $this->domainUrl = preg_replace('(^https?://)', '', $this->monitor->url);
        $this->audits = $this->monitor->lighthouseLatestAudit()->get();
        $this->selectedAudit = $this->audits->first();
    }

    public function changeSelected(LighthouseAudit $audit): void
    {
        $this->selectedAudit = $audit;
    }
};
