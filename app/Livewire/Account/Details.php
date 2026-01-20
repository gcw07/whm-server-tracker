<?php

namespace App\Livewire\Account;

use App\Models\Account;
use Livewire\Component;

class Details extends Component
{
    public Account $account;

    public function mount(Account $account)
    {
        $account->loadMissing(['server']);

        $this->account = $account;
    }

    public function render()
    {
        return view('livewire.account.details')->layoutData(['title' => 'Account Details']);
    }
}
