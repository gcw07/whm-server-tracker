<?php

namespace App\Http\Livewire\Server;

use App\Models\Server;
use LivewireUI\Modal\ModalComponent;

class Delete extends ModalComponent
{
    public $server;

    public function mount(Server $server)
    {
        $this->server = $server;
    }

    public function render()
    {
        return view('livewire.server.delete');
    }

    public static function modalMaxWidth(): string
    {
        return 'xl';
    }

    public function delete()
    {
        $this->server->delete();

        return redirect()->route('servers.index');
    }
}
