<?php

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Listings extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.user.listings', [
            'users' => $this->query(),
        ])->layoutData(['title' => 'Users']);
    }

    protected function query()
    {
        return User::query()->withLastLogin()->orderBy('name')->paginate(50);
    }
}
