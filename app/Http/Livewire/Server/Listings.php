<?php

namespace App\Http\Livewire\Server;

use App\Enums\ServerTypeEnum;
use App\Models\Server;
use Livewire\Component;
use Livewire\WithPagination;

class Listings extends Component
{
    use WithPagination;

    public string|null $serverType = null;

    public function mount()
    {
    }

    public function render()
    {
        return view('livewire.server.listings', [
            'servers' => $this->query(),
        ])->layoutData(['title' => 'Servers']);
    }

    public function filterType($type)
    {
        if (! is_null($type) && ServerTypeEnum::tryFrom($type)) {
            $this->serverType = $type;
        } else {
            $this->serverType = null;
        }
    }

    public function updatedServerType($type)
    {
        if (! is_null($type) && ServerTypeEnum::tryFrom($type)) {
            $this->serverType = $type;
        } else {
            $this->serverType = null;
        }
    }

    protected function query()
    {
        return Server::query()->withCount(['accounts'])->when($this->serverType, function ($query) {
            return $query->where('server_type', $this->serverType);
        })->orderBy('name')->paginate(5);
    }
}
