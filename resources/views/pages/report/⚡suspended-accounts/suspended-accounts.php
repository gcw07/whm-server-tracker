<?php

use App\Models\Account;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Session;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Suspended Accounts Report')] class extends Component
{
    use WithPagination;

    #[Session]
    public string $suspendedSortBy = 'suspend_time';

    #[Session]
    public string $suspendedSortDirection = 'desc';

    public function sort(string $column): void
    {
        $allowedColumns = ['servers.name', 'domain', 'suspend_time'];

        if (! in_array($column, $allowedColumns)) {
            return;
        }

        if ($this->suspendedSortBy === $column) {
            $this->suspendedSortDirection = $this->suspendedSortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->suspendedSortBy = $column;
            $this->suspendedSortDirection = $column === 'suspend_time' ? 'desc' : 'asc';
        }

        $this->resetPage();
    }

    #[Computed]
    public function accounts()
    {
        $query = Account::query()
            ->join('servers', 'servers.id', '=', 'accounts.server_id')
            ->where('accounts.suspended', true)
            ->with('server')
            ->select('accounts.*')
            ->orderBy($this->suspendedSortBy, $this->suspendedSortDirection);

        if ($this->suspendedSortBy === 'servers.name') {
            $query->orderBy('accounts.domain', 'asc');
        }

        return $query->paginate(50);
    }
};
