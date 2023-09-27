<?php

namespace App\Http\Livewire\Server;

use App\Enums\ServerTypeEnum;
use App\Http\Livewire\WithCache;
use App\Models\Server;
use Livewire\Component;
use Livewire\WithPagination;

class Listings extends Component
{
    use WithPagination, WithCache;

    public ?string $serverType = null;

    public ?string $sortBy = null;

    public function mount()
    {
        $this->serverType = $this->getCache('servers', 'serverType');
        $this->sortBy = $this->getCache('servers', 'sortBy');
    }

    public function render()
    {
        return view('livewire.server.listings', [
            'servers' => $this->query(),
        ])->layoutData(['title' => 'Servers']);
    }

    public function filterType($type): void
    {
        if (! is_null($type) && ServerTypeEnum::tryFrom($type)) {
            $this->serverType = $type;
        } else {
            $this->serverType = null;
        }

        $this->putCache('servers', 'serverType', $this->serverType);
    }

    public function updatedServerType($type): void
    {
        if (! is_null($type) && ServerTypeEnum::tryFrom($type)) {
            $this->serverType = $type;
        } else {
            $this->serverType = null;
        }

        $this->putCache('servers', 'serverType', $this->serverType);
    }

    public function sortListingsBy($name): void
    {
        $this->sortBy = match ($name) {
            'newest' => 'newest',
            'accounts' => 'accounts',
            'usage_high' => 'usage_high',
            'usage_low' => 'usage_low',
            default => null,
        };

        $this->putCache('servers', 'sortBy', $this->sortBy);
    }

    protected function query()
    {
        return Server::query()
            ->withCount(['accounts'])
            ->when($this->serverType, function ($query) {
                return $query->where('server_type', $this->serverType);
            })
            ->when($this->sortBy, function ($query) {
                if ($this->sortBy === 'newest') {
                    return $query->orderBy('created_at', 'DESC');
                }

                if ($this->sortBy === 'accounts') {
                    return $query->orderBy('accounts_count', 'DESC');
                }

                if ($this->sortBy === 'usage_high') {
                    return $query->orderBy('settings->disk_percentage', 'DESC');
                }

                // usage low
                return $query->orderBy('settings->disk_percentage', 'ASC');
            }, function ($query) {
                return $query->orderBy('name');
            })
            ->paginate(50);
    }
}
