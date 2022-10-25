<?php

namespace App\Http\Livewire\Account;

use App\Models\Account;
use Livewire\Component;
use Usernotnull\Toast\Concerns\WireToast;

class Details extends Component
{
    use WireToast;

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
