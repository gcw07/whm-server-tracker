<?php

use App\Models\Account;
use App\Models\Server;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Session;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Disk Usage Report')] class extends Component
{
    use WithPagination;

    #[Session]
    public string $diskSortBy = 'disk_pct';

    #[Session]
    public string $diskSortDirection = 'desc';

    public function sort(string $column): void
    {
        $allowedColumns = ['domain', 'disk_pct'];

        if (! in_array($column, $allowedColumns)) {
            return;
        }

        if ($this->diskSortBy === $column) {
            $this->diskSortDirection = $this->diskSortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->diskSortBy = $column;
            $this->diskSortDirection = $column === 'domain' ? 'asc' : 'desc';
        }

        $this->resetPage();
    }

    #[Computed]
    public function servers(): Collection
    {
        return Server::all()->filter(
            fn (Server $server) => $server->settings?->has('disk_percentage')
        )->values();
    }

    #[Computed]
    public function accounts()
    {
        $query = Account::query()
            ->with('server')
            ->selectRaw('*, CASE
                WHEN CAST(SUBSTRING(disk_limit, 1, LENGTH(disk_limit) - 1) AS DECIMAL(10,2)) > 0
                THEN CAST(SUBSTRING(disk_used, 1, LENGTH(disk_used) - 1) AS DECIMAL(10,2))
                     / CAST(SUBSTRING(disk_limit, 1, LENGTH(disk_limit) - 1) AS DECIMAL(10,2)) * 100
                ELSE NULL
            END AS disk_pct');

        if ($this->diskSortBy === 'disk_pct') {
            $query->orderByRaw('disk_pct IS NULL, disk_pct '.$this->diskSortDirection);
        } else {
            $query->orderBy($this->diskSortBy, $this->diskSortDirection);
        }

        return $query->paginate(50);
    }
};
