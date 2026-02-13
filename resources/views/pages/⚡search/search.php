<?php

use App\Models\Account;
use App\Models\Monitor;
use App\Models\Server;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

new #[Title('Search')] class extends Component
{
    #[Url]
    public ?string $search;

    #[Computed]
    public function servers(): \Illuminate\Database\Eloquent\Collection|Collection
    {
        if ($this->search) {
            return Server::query()->withCount(['accounts'])->search($this->search)->orderBy('name')->get();
        }

        return collect();
    }

    #[Computed]
    public function accounts(): \Illuminate\Database\Eloquent\Collection|Collection
    {
        if ($this->search) {
            return Account::query()->with(['server'])->search($this->search)->orderBy('domain')->get();
        }

        return collect();
    }

    #[Computed]
    public function monitors(): \Illuminate\Database\Eloquent\Collection|Collection
    {
        if ($this->search) {
            return Monitor::query()->search($this->search)->orderBy('url')->get();
        }

        return collect();
    }
};
