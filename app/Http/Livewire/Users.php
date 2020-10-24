<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Users extends Component
{
    public function render()
    {
        return view('livewire.users')
            ->layout('components.layouts.app', ['title' => 'Users']);
    }
}
