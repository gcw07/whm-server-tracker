<?php

use App\Models\Monitor;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Session;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Domain Expiry Report')] class extends Component
{
    use WithPagination;

    #[Session]
    public string $domainSortBy = 'expiration_date';

    #[Session]
    public string $domainSortDirection = 'asc';

    public function sort(string $column): void
    {
        $allowedColumns = ['url', 'expiration_date'];

        if (! in_array($column, $allowedColumns)) {
            return;
        }

        if ($this->domainSortBy === $column) {
            $this->domainSortDirection = $this->domainSortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->domainSortBy = $column;
            $this->domainSortDirection = 'asc';
        }

        $this->resetPage();
    }

    #[Computed]
    public function domains()
    {
        $sortColumn = $this->domainSortBy === 'expiration_date'
            ? 'monitor_domain_checks.expiration_date'
            : 'monitors.url';

        return Monitor::query()
            ->join('monitor_domain_checks', 'monitors.id', '=', 'monitor_domain_checks.monitor_id')
            ->where('monitor_domain_checks.enabled', true)
            ->whereNotNull('monitor_domain_checks.expiration_date')
            ->with('domainCheck')
            ->select('monitors.*')
            ->orderBy($sortColumn, $this->domainSortDirection)
            ->paginate(50);
    }
};
