<?php

use App\Models\Account;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Session;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Accounts')] class extends Component
{
    use WithPagination;

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


//
//    public function sortListingsBy($name)
//    {
//        $this->sortBy = match ($name) {
//            'newest' => 'newest',
//            'usage_high' => 'usage_high',
//            'usage_low' => 'usage_low',
//            default => null,
//        };
//
//        $this->putCache('accounts', 'sortBy', $this->sortBy);
//    }
//
//    public function filterListingsBy($name)
//    {
//        $this->filterBy = match ($name) {
//            'duplicates' => 'duplicates',
//            'suspended' => 'suspended',
//            default => null,
//        };
//
//        $this->putCache('accounts', 'filterBy', $this->filterBy);
//    }

    #[Computed]
    public function accounts()
    {
        return Account::query()
            ->with(['server'])
            ->select('*')
            ->selectRaw('(disk_used / disk_limit) * 100 as sort_disk_usage')
            ->when($this->sortBy, function ($query) {
                if ($this->sortBy === 'newest') {
                    return $query->orderBy('created_at', 'DESC')->orderBy('domain');
                }

                if ($this->sortBy === 'usage_high') {
                    return $query->orderBy('sort_disk_usage', 'DESC');
                }

                // usage low
                return $query->orderBy('sort_disk_usage', 'ASC');
            }, function ($query) {
                return $query->orderBy('domain');
            })
            ->when($this->filterBy, function ($query) {
                if ($this->filterBy === 'duplicates') {
                    return $query->whereExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('accounts', 'a')
                            ->whereColumn('a.domain', 'accounts.domain')
                            ->limit(1)
                            ->offset(1);
                    });
                }

                if ($this->filterBy === 'suspended') {
                    return $query->where('suspended', true);
                }

                return $query;
            })
            ->paginate(50);
    }
};
