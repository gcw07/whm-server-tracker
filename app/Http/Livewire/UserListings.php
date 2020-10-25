<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User as UserData;

class UserListings extends Component
{
    public function render()
    {
        return view('livewire.user-listings', [
            'users' => UserData::all()
        ])->layout('components.layouts.app', ['title' => 'Users']);
    }
}
