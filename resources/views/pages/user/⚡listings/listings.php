<?php

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Users')] class extends Component
{
    #[Computed]
    public function users()
    {
        return User::query()
            ->withLastLogin()
            ->orderBy('name')
            ->paginate(50);
    }
};
