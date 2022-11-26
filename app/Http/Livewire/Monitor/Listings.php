<?php

namespace App\Http\Livewire\Monitor;

use App\Http\Livewire\WithCache;
use App\Models\Monitor;
use Livewire\Component;
use Livewire\WithPagination;

class Listings extends Component
{
    use WithPagination, WithCache;

    public string|bool $hasIssues = 'false';

    public string|null $sortBy = null;

    public string|null $filterBy = null;

    public function mount()
    {
        $this->hasIssues = $this->getCache('monitors', 'hasIssues', false);
        $this->sortBy = $this->getCache('monitors', 'sortBy');
        $this->filterBy = $this->getCache('monitors', 'filterBy');
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

    public function filterListingsBy($name)
    {
        $this->filterBy = match ($name) {
            'disabled' => 'disabled',
            default => null,
        };

        $this->putCache('monitors', 'filterBy', $this->filterBy);
    }

    protected function query()
    {
        return Monitor::query()
            ->when($this->hasIssues, function ($query) {
                return $query
                    ->where(function ($query) {
                        $query->where('uptime_check_enabled', true)
                            ->orWhere('certificate_check_enabled', true)
                            ->orWhere('blacklist_check_enabled', true);
                    })
                    ->where(function ($query) {
                        $query->where('uptime_status', 'down')
                            ->orWhere('certificate_status', 'invalid')
                            ->orWhere('blacklist_status', 'invalid');
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
            ->when($this->filterBy, function ($query) {
                if ($this->filterBy === 'disabled') {
                    return $query->where(function ($query) {
                        $query->where('uptime_check_enabled', false)
                            ->orWhere('certificate_check_enabled', false)
                            ->orWhere('blacklist_check_enabled', false);
                    });
                }

                return $query;
            })
            ->paginate(50);
    }

    protected function issueTypeCountQuery(): array
    {
        return [
            'all' => Monitor::query()->count(),
            'issues' => Monitor::query()
                ->where(function ($query) {
                    $query->where('uptime_check_enabled', true)
                        ->orWhere('certificate_check_enabled', true)
                        ->orWhere('blacklist_check_enabled', true);
                })
                ->where(function ($query) {
                    $query->where('uptime_status', 'down')
                        ->orWhere('certificate_status', 'invalid')
                        ->orWhere('blacklist_status', 'invalid');
                })
                ->count(),
        ];
    }
}
