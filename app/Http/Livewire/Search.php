<?php

namespace App\Http\Livewire;

use App\Models\Account;
use App\Models\Server;
use Illuminate\Support\Facades\Validator;
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
        ])->layoutData(['title' => 'Search']);
    }

    public function clear()
    {
        $this->reset('q');
    }

    protected function searchServers($term)
    {
        if (! empty($term)) {
            return Server::query()->withCount(['accounts'])->search($term)->orderBy('name')->get();
        }

        return collect();
    }

    protected function searchAccounts($term)
    {
        if (! empty($term)) {
            return Account::query()->with(['server'])->search($term)->orderBy('domain')->get();
        }

        return collect();
    }
}
