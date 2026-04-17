<?php

use App\Models\Account;
use App\Models\Monitor;
use App\Models\Server;
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

    #[Computed]
    public function suspendedAccounts(): int
    {
        return Account::query()->where('suspended', true)->count();
    }

    #[Computed]
    public function monitorsDown(): int
    {
        return Monitor::query()
            ->where('uptime_check_enabled', true)
            ->where('uptime_status', 'down')
            ->count();
    }

    #[Computed]
    public function diskWarningServers(): Collection
    {
        return Server::query()
            ->whereRaw("CAST(json_unquote(json_extract(`settings`, '$.\"disk_percentage\"')) AS FLOAT) >= 80")
            ->orderByRaw("CAST(json_unquote(json_extract(`settings`, '$.\"disk_percentage\"')) AS FLOAT) DESC")
            ->limit(5)
            ->get();
    }
};
