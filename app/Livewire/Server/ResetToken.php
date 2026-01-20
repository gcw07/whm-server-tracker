<?php

namespace App\Livewire\Server;

use App\Models\Server;

class ResetToken
{
    public $server;

    public function mount(Server $server): void
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
