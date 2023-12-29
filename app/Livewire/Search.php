<?php

namespace App\Livewire;

use App\Models\Account;
use App\Models\Monitor;
use App\Models\Server;
use Illuminate\Support\Collection;
use Livewire\Component;

class Search extends Component
{
    public $q;

    protected $queryString = ['q'];

    public function mount()
    {
        $this->q = request()->input('q', '');
    }

    public function render()
    {
        $term = $this->q;

        return view('livewire.search', [
            'servers' => $this->searchServers($term),
            'accounts' => $this->searchAccounts($term),
            'monitors' => $this->searchMonitors($term),
        ])->layoutData(['title' => 'Search']);
    }

    public function clear()
    {
        $this->reset('q');
    }

    protected function searchServers($term): Collection
    {
        if (! empty($term)) {
            return Server::query()->withCount(['accounts'])->search($term)->orderBy('name')->get();
        }

        return collect();
    }

    protected function searchAccounts($term): Collection
    {
        if (! empty($term)) {
            return Account::query()->with(['server'])->search($term)->orderBy('domain')->get();
        }

        return collect();
    }

    protected function searchMonitors($term): Collection
    {
        if (! empty($term)) {
            return Monitor::query()->search($term)->orderBy('url')->get();
        }

        return collect();
    }
}
