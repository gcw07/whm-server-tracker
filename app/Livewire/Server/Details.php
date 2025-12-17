<?php

namespace App\Livewire\Server;

use App\Jobs\FetchServerDataJob;
use App\Models\Monitor;
use App\Models\Server;
use Livewire\Component;
use Usernotnull\Toast\Concerns\WireToast;

class Details extends Component
{
    use WireToast;

    public Server $server;

    public array $monitoredAccounts;

    public function mount(Server $server): void
    {
        $server->loadMissing(['accounts'])->loadCount(['accounts']);

        $this->server = $server;
        $this->monitoredAccounts = $this->getMonitoredAccounts();
    }

    public function getMonitoredAccounts(): array
    {
        $domains = $this->server->accounts->pluck('domain_url');

        return Monitor::select(['id', 'url'])
            ->whereIn('url', $domains)
            ->get()
            ->mapWithKeys(fn ($monitor) => [$monitor->url->getHost() => $monitor->id])
            ->toArray();
    }

    public function getMonitorId(string $domain): ?int
    {
        return $this->monitoredAccounts[$domain] ?? null;
    }

    public function render()
    {
        return view('livewire.server.details')->layoutData(['title' => 'Server Details']);
    }

    public function refresh(): void
    {
        FetchServerDataJob::dispatch($this->server);

        toast()->success('The server details will be refreshed shortly.')->push();
    }
}
