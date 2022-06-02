<?php

namespace App\Http\Livewire\User;

use App\Models\User;
use Livewire\Component;

class Listings extends Component
{
    public function render()
    {
        return view('livewire.user.listings', [
            'users' => $this->query(),
        ])->layoutData(['title' => 'Users']);
    }

    protected function query()
    {
        return User::query()->orderBy('name')->paginate(50);
    }
}
