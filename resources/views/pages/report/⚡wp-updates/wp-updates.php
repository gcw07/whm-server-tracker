<?php

use App\Models\Monitor;
use App\Models\MonitorWordPressCheck;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Session;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('WP Updates Report')] class extends Component
{
    use WithPagination;

    #[Session]
    public string $sortBy = 'url';

    #[Session]
    public string $sortDirection = 'asc';

    #[Session]
    public string $filterBy = 'none';

    public function sort(string $column): void
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
            ->with(['wordpressCheck', 'accounts.server'])
            ->whereHas('wordpressCheck', fn (Builder $q) => $q->where('enabled', true))
            ->when($this->filterBy, function (Builder $query) {
                if ($this->filterBy === 'agent_installed') {
                    return $query->whereHas('wordpressCheck', fn (Builder $q) => $q->where('check_source', 'agent'));
                }
                if ($this->filterBy === 'agent_not_installed') {
                    return $query->where(fn (Builder $q) => $q
                        ->whereDoesntHave('wordpressCheck')
                        ->orWhereHas('wordpressCheck', fn (Builder $q) => $q->where('check_source', '!=', 'agent'))
                    );
                }

                return $query;
            })
            ->when($this->sortBy, function (Builder $query) {
                if ($this->sortBy === 'wordpress_version') {
                    return $query->orderBy(
                        MonitorWordPressCheck::select('wordpress_version')
                            ->whereColumn('monitor_wordpress_checks.monitor_id', 'monitors.id'),
                        $this->sortDirection
                    );
                }

                return $query->orderBy($this->sortBy, $this->sortDirection);
            })
            ->paginate(50);
    }
};
