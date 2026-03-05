<?php

use App\Models\Account;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Account Details')] class extends Component
{
    public Account $account;

    public function mount(Account $account): void
    {
        $account->loadMissing(['server']);
        $account->load(['emails' => fn ($query) => $query->orderBy('disk_used', 'desc')]);

        $this->account = $account;
    }
};
