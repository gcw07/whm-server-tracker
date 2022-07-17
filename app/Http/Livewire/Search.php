<?php

namespace App\Http\Livewire;

use App\Models\Account;
use App\Models\Server;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class Search extends Component
{
    public function mount()
    {

    }

    public function render()
    {
        $data = Validator::make(
            ['term' => request()->input('q')],
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
        return Server::query()->search($term)->orderBy('name')->get();
    }

    protected function searchAccounts($term)
    {
        return Account::query()->with(['server'])->search($term)->orderBy('domain')->get();
    }
}
