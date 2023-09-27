<?php

namespace App\Http\Livewire\Account;

use App\Http\Livewire\WithCache;
use App\Models\Account;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Listings extends Component
{
    use WithPagination, WithCache;

    public ?string $sortBy = null;

    public ?string $filterBy = null;

    public function mount()
    {
        $this->sortBy = $this->getCache('accounts', 'sortBy');
        $this->filterBy = $this->getCache('accounts', 'filterBy');
    }

    public function render()
    {
        return view('livewire.account.listings', [
            'accounts' => $this->query(),
        ])->layoutData(['title' => 'Accounts']);
    }

    public function sortListingsBy($name)
    {
        $this->sortBy = match ($name) {
            'newest' => 'newest',
            'usage_high' => 'usage_high',
            'usage_low' => 'usage_low',
            default => null,
        };

        $this->putCache('accounts', 'sortBy', $this->sortBy);
    }

    public function filterListingsBy($name)
    {
        $this->filterBy = match ($name) {
            'duplicates' => 'duplicates',
            default => null,
        };

        $this->putCache('accounts', 'filterBy', $this->filterBy);
    }

    protected function query()
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

                return $query;
            })
            ->paginate(50);
    }
}
