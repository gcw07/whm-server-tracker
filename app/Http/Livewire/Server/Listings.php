<?php

namespace App\Http\Livewire\Server;

use App\Models\Server;
use Livewire\Component;

class Listings extends Component
{
    public $servers;

    public function mount()
    {
        $this->servers = Server::query()->withCount(['accounts'])->orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.server.listings')
            ->layoutData(['title' => 'Servers']);
    }
}
