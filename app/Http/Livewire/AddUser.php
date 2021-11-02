<?php

namespace App\Http\Livewire;

use LivewireUI\Modal\ModalComponent;

class AddUser extends ModalComponent
{
    public function render()
    {
        return view('livewire.add-user');
    }
}
