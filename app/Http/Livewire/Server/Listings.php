<?php

namespace App\Http\Livewire\Server;

use App\Enums\ServerTypeEnum;
use App\Models\Server;
use Livewire\Component;

class Listings extends Component
{
    public string|null $serverType;

    public function mount()
    {
        $this->serverType = null;
    }

    public function render()
    {
        return view('livewire.server.listings', [
            'servers' => $this->query(),
        ])->layoutData(['title' => 'Servers']);
    }

    public function filterType($type)
    {
        if (ServerTypeEnum::tryFrom($type)) {
            $this->serverType = $type;
        } else {
            $this->serverType = null;
        }
    }

    protected function query()
    {
        return Server::query()->withCount(['accounts'])->when($this->serverType, function ($query) {
            return $query->where('server_type', $this->serverType);
        })->orderBy('name')->get();
    }
}
