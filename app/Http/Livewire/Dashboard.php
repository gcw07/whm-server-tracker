<?php

namespace App\Http\Livewire;

use App\Models\Account;
use App\Models\Server;
use App\Models\User;
use Livewire\Component;

class Dashboard extends Component
{
    public function mount()
    {
    }

    public function render()
    {
        return view('livewire.dashboard', [
            'totalServers' => $this->totalServers(),
            'totalAccounts' => $this->totalAccounts(),
            'totalUsers' => $this->totalUsers(),
            'serverTypes' => $this->serverTypeQuery(),
            'recentAccounts' => $this->recentAccounts(),
        ])->layoutData(['title' => 'Dashboard']);
    }

    protected function totalServers()
    {
        return Server::query()->count();
    }

    protected function totalAccounts()
    {
        return Account::query()->count();
    }

    protected function totalUsers()
    {
        return User::query()->count();
    }

    protected function serverTypeQuery(): object|null
    {
        return Server::toBase()
            ->selectRaw("count(case when server_type = 'dedicated' then 1 end) as dedicated")
            ->selectRaw("count(case when server_type = 'reseller' then 1 end) as reseller")
            ->selectRaw("count(case when server_type = 'vps' then 1 end) as vps")
            ->first();
    }

    protected function recentAccounts()
    {
        return Account::query()->with(['server'])->latest()->limit(10)->get();
    }
}
