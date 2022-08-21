<?php

namespace App\Http\Livewire\Server;

use App\Models\Server;
use LivewireUI\Modal\ModalComponent;
use Usernotnull\Toast\Concerns\WireToast;

class Delete extends ModalComponent
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
        return view('livewire.server.delete');
    }

    public static function modalMaxWidth(): string
    {
        return 'xl';
    }

    public function delete()
    {
        $this->server->removeMonitors();
        $this->server->delete();

        toast()->success('The server was deleted successfully.')->pushOnNextPage();

        return redirect()->route('servers.index');
    }
}
