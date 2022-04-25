<?php

namespace App\Http\Livewire\Account;

use App\Models\Account;
use Livewire\Component;

class Listings extends Component
{
    public $accounts;

    public function mount()
    {
        $this->accounts = Account::query()->orderBy('domain')->get();
    }

    public function render()
    {
        return view('livewire.account.listings')
            ->layoutData(['title' => 'Accounts']);
    }
}
