<?php

use App\Models\Monitor;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Session;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Monitors')] class extends Component
{
    use WithPagination;

    #[Session]
    public string $monitorType = 'all';

    #[Session]
    public string $sortBy = 'name';

    #[Session]
    public string $sortDirection = 'asc';

    #[Session]
    public string $filterBy = 'none';

    public function sort($column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function removeAllFilters(): void
    {
        $this->filterBy = 'none';
    }

    #[Computed]
    public function monitors()
    {
        return Monitor::query()
            ->when($this->monitorType !== 'all', function (Builder $query) {
                return $query
                    ->where(function ($query) {
                        $query
                            ->where('uptime_check_enabled', true)
                            ->orWhere('certificate_check_enabled', true);
                    })
                    ->where(function ($query) {
                        $query
                            ->where('uptime_status', 'down')
                            ->orWhere('certificate_status', 'invalid');
                    });
            })
            ->when($this->sortBy, function (Builder $query) {
                //                if ($this->sortBy === 'newest') {
                //                    return $query->orderBy('created_at', $this->sortDirection);
                //                }

                return $query->orderBy('url', $this->sortDirection);
            })
            ->when($this->filterBy, function (Builder $query) {
                if ($this->filterBy === 'disabled') {
                    return $query->where(function ($query) {
                        $query->where('uptime_check_enabled', false)
                            ->orWhere('certificate_check_enabled', false)
                            ->orWhere('blacklist_check_enabled', false);
                    });
                }

                if ($this->filterBy === 'on_cloudflare') {
                    return $query->where(function ($query) {
                        $query->where('is_on_cloudflare', true);
                    });
                }

                if ($this->filterBy === 'not_on_cloudflare') {
                    return $query->where(function ($query) {
                        $query->where('is_on_cloudflare', false);
                    });
                }

                return $query;
            })
            ->paginate(50);
    }

    #[Computed]
    public function issuesCount()
    {
        return [
            'all' => Monitor::query()->count(),
            'issues' => Monitor::query()
                ->where(function ($query) {
                    $query
                        ->where('uptime_check_enabled', true)
                        ->orWhere('certificate_check_enabled', true);
                })
                ->where(function ($query) {
                    $query
                        ->where('uptime_status', 'down')
                        ->orWhere('certificate_status', 'invalid');
                })
                ->count(),
        ];
    }
};
