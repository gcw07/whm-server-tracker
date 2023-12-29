<?php

namespace App\Livewire\Server;

use App\Models\Server;
use LivewireUI\Modal\ModalComponent;
use Usernotnull\Toast\Concerns\WireToast;

class ResetToken extends ModalComponent
{
    use WireToast;

    public $server;

    public function mount(Server $server)
    {
        abort_if(auth()->guest(), 401);

        $this->server = $server;
    }

    public function render()
    {
        return view('livewire.server.reset-token');
    }

    public static function modalMaxWidth(): string
    {
        return 'xl';
    }
}
