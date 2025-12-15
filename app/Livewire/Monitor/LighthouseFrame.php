<?php

namespace App\Livewire\Monitor;

use App\Models\LighthouseAudit;

class LighthouseFrame
{
    public function __invoke(LighthouseAudit $audit): ?string
    {
        return $audit->report;
    }
}
