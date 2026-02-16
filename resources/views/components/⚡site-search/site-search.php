<?php

use App\Models\Account;
use App\Models\Monitor;
use App\Models\Server;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public ?string $siteSearch = null;

    #[Computed]
    public function servers(): \Illuminate\Database\Eloquent\Collection|Collection
    {
        if ($this->siteSearch) {
            return Server::query()->withCount(['accounts'])->search($this->siteSearch)->orderBy('name')->get();
        }

        return collect();
    }

    #[Computed]
    public function accounts(): \Illuminate\Database\Eloquent\Collection|Collection
    {
        if ($this->siteSearch) {
            return Account::query()->with(['server:id,name'])->search($this->siteSearch)->orderBy('domain')->get();
        }

        return collect();
    }

    #[Computed]
    public function monitors(): \Illuminate\Database\Eloquent\Collection|Collection
    {
        if ($this->siteSearch) {
            return Monitor::query()->search($this->siteSearch)->orderBy('url')->get();
        }

        return collect();
    }
};
