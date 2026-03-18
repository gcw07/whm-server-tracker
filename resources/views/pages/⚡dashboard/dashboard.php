<?php

use App\Models\Account;
use App\Models\Monitor;
use App\Models\Server;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Dashboard')] class extends Component
{
    #[Computed]
    public function totalServers(): int
    {
        return Server::query()->count();
    }

    #[Computed]
    public function totalAccounts(): int
    {
        return Account::query()->count();
    }

    #[Computed]
    public function totalMonitors(): int
    {
        return Monitor::query()->count();
    }

    #[Computed]
    public function totalUsers(): int
    {
        return User::query()->count();
    }

    #[Computed]
    public function serverTypes(): ?object
    {
        return Server::toBase()
            ->selectRaw("count(case when server_type = 'dedicated' then 1 end) as dedicated")
            ->selectRaw("count(case when server_type = 'reseller' then 1 end) as reseller")
            ->selectRaw("count(case when server_type = 'vps' then 1 end) as vps")
            ->first();
    }

    #[Computed]
    public function sitesWithIssues(): int
    {
        return Monitor::query()->withIssues()->count();
    }

    #[Computed]
    public function serversWithIssues(): int
    {
        return Server::query()
            ->whereRaw("CAST(json_unquote(json_extract(`settings`, '$.\"disk_percentage\"')) AS FLOAT) >= 90")
            ->count();
    }

    #[Computed]
    public function recentAccounts(): Collection
    {
        return Account::query()->with(['server'])->latest()->limit(10)->get();
    }
};
