<?php

namespace App\Http\Livewire\User;

use Livewire\Component;

class Create extends Component
{
    public function render()
    {
        return view('livewire.user.create')->layoutData(['title' => 'Create User']);
    }
}
