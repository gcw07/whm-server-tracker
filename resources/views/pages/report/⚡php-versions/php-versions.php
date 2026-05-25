<?php

use App\Models\Account;
use App\Services\PhpVersions;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Session;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('PHP Versions Report')] class extends Component
{
    use WithPagination;

    #[Session]
    public string $sortBy = 'php_version';

    #[Session]
    public string $sortDirection = 'asc';

    public function sort(string $column): void
    {
        $allowedColumns = ['domain', 'php_version'];

        if (! in_array($column, $allowedColumns)) {
            return;
        }

        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    #[Computed]
    public function versionSummary(): Collection
    {
        $versions = PhpVersions::all();

        return Account::query()
            ->select('php_version', DB::raw('count(*) as account_count'))
            ->whereNotNull('php_version')
            ->groupBy('php_version')
            ->orderBy('php_version')
            ->get()
            ->map(function ($group) use ($versions) {
                $key = substr($group->php_version, 3);
                $info = $versions->get($key, [
                    'name' => $group->php_version,
                    'status' => 'unknown',
                    'color' => 'zinc',
                ]);

                return [
                    'raw' => $group->php_version,
                    'name' => $info['name'],
                    'status' => $info['status'],
                    'color' => $info['color'],
                    'count' => $group->account_count,
                ];
            });
    }

    #[Computed]
    public function accounts()
    {
        return Account::query()
            ->with('server')
            ->whereNotNull('php_version')
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(50);
    }
};
