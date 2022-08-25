<?php

namespace App\Http\Livewire\Monitor;

use Livewire\Component;
use Spatie\UptimeMonitor\Models\Monitor;
use Usernotnull\Toast\Concerns\WireToast;

class Details extends Component
{
    use WireToast;

    public Monitor $monitor;

    public function mount(Monitor $monitor)
    {
//        $server->loadMissing(['accounts' => function ($query) {
//            return $query->orderBy('domain');
//        }, 'accounts.server'])->loadCount(['accounts']);

        $this->monitor = $monitor;
    }

    public function render()
    {
        return view('livewire.monitor.details')->layoutData(['title' => 'Monitor Details']);
    }
}
