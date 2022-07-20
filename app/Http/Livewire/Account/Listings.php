<?php

namespace App\Http\Livewire\Account;

use App\Models\Account;
use Livewire\Component;
use Livewire\WithPagination;

class Listings extends Component
{
    use WithPagination;

    public function mount()
    {
    }

    public function render()
    {
        return view('livewire.account.listings', [
            'accounts' => $this->query(),
        ])->layoutData(['title' => 'Accounts']);
    }

    protected function query()
    {
        return Account::query()->with(['server'])->orderBy('domain')->paginate(50);
    }
}
