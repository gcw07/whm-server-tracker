<?php

namespace App\Livewire;

use App\Models\Account;
use App\Models\Server;
use App\Models\User;
use Livewire\Component;
use Spatie\UptimeMonitor\Models\Monitor;

class Dashboard extends Component
{
    public function mount() {}

    public function render()
    {
        return view('livewire.dashboard', [
            'totalServers' => $this->totalServers(),
            'totalAccounts' => $this->totalAccounts(),
            'totalMonitors' => $this->totalMonitors(),
            'serverTypes' => $this->serverTypeQuery(),
            'sitesWithIssues' => $this->sitesWithIssues(),
            'serversWithIssues' => $this->serversWithIssues(),
            'recentAccounts' => $this->recentAccounts(),
        ])->layoutData(['title' => 'Dashboard']);
    }

    protected function totalServers(): int
    {
        return Server::query()->count();
    }

    protected function totalAccounts(): int
    {
        return Account::query()->count();
    }

    protected function totalMonitors(): int
    {
        return Monitor::query()->count();
    }

    protected function totalUsers(): int
    {
        return User::query()->count();
    }

    protected function serverTypeQuery(): ?object
    {
        return Server::toBase()
            ->selectRaw("count(case when server_type = 'dedicated' then 1 end) as dedicated")
            ->selectRaw("count(case when server_type = 'reseller' then 1 end) as reseller")
            ->selectRaw("count(case when server_type = 'vps' then 1 end) as vps")
            ->first();
    }

    protected function sitesWithIssues(): int
    {
        return Monitor::query()
            ->where(function ($query) {
                $query->where('uptime_check_enabled', true)
                    ->orWhere('certificate_check_enabled', true);
                //                    ->orWhere('blacklist_check_enabled', true);
            })
            ->where(function ($query) {
                $query->where('uptime_status', 'down')
                    ->orWhere('certificate_status', 'invalid');
                //                    ->orWhere('blacklist_status', 'invalid');
            })
            ->count();
    }

    protected function serversWithIssues(): int
    {
        return Server::query()
            ->whereRaw("CAST(json_unquote(json_extract(`settings`, '$.\"disk_percentage\"')) AS FLOAT) >= 90")
            ->count();
    }

    protected function recentAccounts()
    {
        return Account::query()->with(['server'])->latest()->limit(10)->get();
    }
}
