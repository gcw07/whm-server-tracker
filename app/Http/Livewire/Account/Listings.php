<?php

namespace App\Http\Livewire\Account;

use App\Http\Livewire\WithCache;
use App\Models\Account;
use Livewire\Component;
use Livewire\WithPagination;

class Listings extends Component
{
    use WithPagination, WithCache;

    public string|null $sortBy = null;

    public function mount()
    {
        $this->sortBy = $this->getCache('accounts', 'sortBy');
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

        $this->putCache('accounts','sortBy', $this->sortBy);
    }

    protected function query()
    {
        return Account::query()
            ->with(['server'])
            ->select('*')
            ->selectRaw('(disk_used / disk_limit) * 100 as sort_disk_usage')
            ->when($this->sortBy, function ($query) {
                if ($this->sortBy === 'newest') {
                    return $query->orderBy('created_at', 'DESC');
                }

                if ($this->sortBy === 'usage_high') {
                    return $query->orderBy('sort_disk_usage', 'DESC');
                }

                // usage low
                return $query->orderBy('sort_disk_usage', 'ASC');
            }, function($query) {
                return $query->orderBy('domain');
            })
            ->paginate(50);
    }
}
