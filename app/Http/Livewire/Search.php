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
        $this->q = request()->input('q');
    }

    public function render()
    {
        $data = Validator::make(
            ['term' => $this->q],
            ['term' => ['required', 'string']],
            ['required' => 'The :attribute field is required'],
        )->validate();

        return view('livewire.search', [
            'servers' => $this->searchServers($data['term']),
            'accounts' => $this->searchAccounts($data['term']),
        ])->layoutData(['title' => 'Search']);
    }

    protected function searchServers($term)
    {
        return Server::query()->withCount(['accounts'])->search($term)->orderBy('name')->get();
    }

    protected function searchAccounts($term)
    {
        return Account::query()->with(['server'])->search($term)->orderBy('domain')->get();
    }
}
