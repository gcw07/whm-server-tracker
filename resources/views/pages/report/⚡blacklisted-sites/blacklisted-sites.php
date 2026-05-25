<?php

use App\Models\Monitor;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Session;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Blacklisted Sites Report')] class extends Component
{
    use WithPagination;

    #[Session]
    public string $blacklistSortBy = 'status';

    #[Session]
    public string $blacklistSortDirection = 'asc';

    public function sort(string $column): void
    {
        $allowedColumns = ['url', 'status'];

        if (! in_array($column, $allowedColumns)) {
            return;
        }

        if ($this->blacklistSortBy === $column) {
            $this->blacklistSortDirection = $this->blacklistSortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->blacklistSortBy = $column;
            $this->blacklistSortDirection = 'asc';
        }

        $this->resetPage();
    }

    #[Computed]
    public function monitors()
    {
        $query = Monitor::query()
            ->join('monitor_blacklist_checks', 'monitors.id', '=', 'monitor_blacklist_checks.monitor_id')
            ->where('monitor_blacklist_checks.enabled', true)
            ->with(['blacklistCheck', 'blacklistCheck.results'])
            ->select('monitors.*');

        if ($this->blacklistSortBy === 'status') {
            $direction = $this->blacklistSortDirection === 'asc' ? '' : 'DESC';
            $query->orderByRaw("CASE
                WHEN monitor_blacklist_checks.status = 'invalid' THEN 0
                WHEN monitor_blacklist_checks.status = 'not yet checked' THEN 1
                ELSE 2
            END {$direction}")
                ->orderBy('monitors.url');
        } else {
            $query->orderBy('monitors.url', $this->blacklistSortDirection);
        }

        return $query->paginate(50);
    }
};
