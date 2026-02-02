<?php

use App\Jobs\FetchServerDataJob;
use App\Models\Monitor;
use App\Models\Server;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Server Details')] class extends Component
{
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

    public function refresh(): void
    {
        FetchServerDataJob::dispatch($this->server)->onQueue('high');

        Flux::toast(
            text: 'The server details will be refreshed shortly.',
            heading: 'Refreshing...',
            variant: 'success',
        );
    }

    public function delete(): void {
        $this->server->removeMonitors();
        $this->server->delete();

        $this->modal('delete-server')->close();

        Flux::toast(
            text: 'The server was deleted successfully.',
            heading: 'Deleted...',
            variant: 'success',
        );

        $this->redirectRoute('servers.index', [],true, true);
    }
};
