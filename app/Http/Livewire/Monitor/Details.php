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
//        $server->loadMissing(['accounts' => function ($query) {
//            return $query->orderBy('domain');
//        }, 'accounts.server'])->loadCount(['accounts']);

        $this->monitor = $monitor;
        $this->domainUrl = preg_replace("(^https?://)", "", $this->monitor->url);
    }

    public function render()
    {
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
}
