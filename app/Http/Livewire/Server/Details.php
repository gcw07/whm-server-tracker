<?php

namespace App\Http\Livewire\Server;

use App\Models\Server;
use Livewire\Component;

class Details extends Component
{
    public Server $server;

    public function mount(Server $server)
    {
        $server->loadMissing(['accounts' => function ($query) {
            return $query->orderBy('domain');
        }])->loadCount(['accounts']);

        $this->server = $server;
    }

    public function render()
    {
        return view('livewire.server.details')->layoutData(['title' => 'Server Details']);
    }
}
