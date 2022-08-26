<?php

namespace App\Http\Livewire\Monitor;

use App\Http\Livewire\WithCache;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\UptimeMonitor\Models\Monitor;

class Listings extends Component
{
    use WithPagination, WithCache;

    public string|bool $hasIssues = 'false';

    public string|null $sortBy = null;

    public function mount()
    {
        $this->hasIssues = $this->getCache('monitors', 'hasIssues', false);
        $this->sortBy = $this->getCache('monitors', 'sortBy');
    }

    public function render()
    {
        return view('livewire.monitor.listings', [
            'monitors' => $this->query(),
            'issueTypeCounts' => $this->issueTypeCountQuery(),
        ])->layoutData(['title' => 'Monitors']);
    }

    public function filterIssues($type)
    {
        if ($type === 'true') {
            $this->hasIssues = true;
        } else {
            $this->hasIssues = false;
        }

        $this->putCache('monitors', 'hasIssues', $this->hasIssues);
    }

    public function updatedHasIssues($type)
    {
        if ($type === 'true') {
            $this->hasIssues = true;
        } else {
            $this->hasIssues = false;
        }

        $this->putCache('monitors', 'hasIssues', $this->hasIssues);
    }

    public function sortListingsBy($name)
    {
        $this->sortBy = match ($name) {
            'alpha_reversed' => 'alpha_reversed',
            default => null,
        };

        $this->putCache('monitors', 'sortBy', $this->sortBy);
    }

    protected function query()
    {
        return Monitor::query()
            ->when($this->hasIssues, function ($query) {
                return $query
                    ->where(function($query) {
                        $query->where('uptime_check_enabled', true)
                            ->orWhere('certificate_check_enabled', true);
                    })
                    ->where(function($query) {
                        $query->where('uptime_status', 'down')
                            ->orWhere('certificate_status', 'invalid');
                    });
            })
            ->when($this->sortBy, function ($query) {
                if ($this->sortBy === 'alpha_reversed') {
                    return $query->orderBy('url', 'DESC');
                }

                return $query->orderBy('url');
            }, function ($query) {
                return $query->orderBy('url');
            })
            ->paginate(50);
    }

    protected function issueTypeCountQuery(): array
    {
        return [
            'all' => Monitor::query()->count(),
            'issues' => Monitor::query()
                ->where(function($query) {
                    $query->where('uptime_check_enabled', true)
                        ->orWhere('certificate_check_enabled', true);
                })
                ->where(function($query) {
                    $query->where('uptime_status', 'down')
                        ->orWhere('certificate_status', 'invalid');
                })
                ->count(),
        ];
    }
}
