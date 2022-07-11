<?php

namespace App\Http\Livewire\Server;

use LivewireUI\Modal\ModalComponent;

class Delete extends ModalComponent
{
    public function render()
    {
        return view('livewire.server.delete');
    }
}
