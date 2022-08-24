<?php

namespace App\Http\Livewire\Monitor;

use App\Enums\ServerTypeEnum;
use App\Http\Livewire\WithCache;
use App\Models\Server;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\UptimeMonitor\Models\Monitor;

class Listings extends Component
{
    use WithPagination, WithCache;

    public string|null $monitorType = null;

    public string|null $sortBy = null;

    public function mount()
    {
        $this->monitorType = $this->getCache('monitors', 'monitorType');
        $this->sortBy = $this->getCache('monitors', 'sortBy');
    }

    public function render()
    {
        return view('livewire.monitor.listings', [
            'monitors' => $this->query(),
        ])->layoutData(['title' => 'Monitors']);
    }

    public function filterType($type)
    {
        if ($type === 'issues') {
            $this->monitorType = $type;
        } else {
            $this->monitorType = null;
        }

        $this->putCache('monitors', 'monitorType', $this->monitorType);
    }

    public function updatedMonitorType($type)
    {
        if ($type === 'issues') {
            $this->monitorType = $type;
        } else {
            $this->monitorType = null;
        }

        $this->putCache('monitors', 'monitorType', $this->monitorType);
    }

    public function sortListingsBy($name)
    {
        $this->sortBy = match ($name) {
            'newest' => 'newest',
            'accounts' => 'accounts',
            'usage_high' => 'usage_high',
            'usage_low' => 'usage_low',
            default => null,
        };

        $this->putCache('monitors', 'sortBy', $this->sortBy);
    }

    protected function query()
    {
        return Monitor::query()
            ->when($this->monitorType, function ($query) {
                return $query->where('uptime_status', $this->monitorType);
            })
            ->when($this->sortBy, function ($query) {
//                if ($this->sortBy === 'newest') {
//                    return $query->orderBy('created_at', 'DESC');
//                }
//
//                if ($this->sortBy === 'accounts') {
//                    return $query->orderBy('accounts_count', 'DESC');
//                }
//
//                if ($this->sortBy === 'usage_high') {
//                    return $query->orderBy('settings->disk_percentage', 'DESC');
//                }
//
//                // usage low
//                return $query->orderBy('settings->disk_percentage', 'ASC');
            }, function ($query) {
                return $query->orderBy('url');
            })
            ->paginate(50);
    }
}
