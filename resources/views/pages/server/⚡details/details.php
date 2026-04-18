<?php

use App\Jobs\FetchServerDetailsJob;
use App\Models\Server;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Title('Server Details')] class extends Component
{
    public int $serverId;

    #[Validate(['required', 'string'])]
    public ?string $newToken = null;

    public function mount(int $server): void
    {
        $this->serverId = $server;
    }

    #[Computed]
    public function server(): Server
    {
        return Server::with(['accounts' => fn ($q) => $q->withCount('emails')])
            ->withCount('accounts')
            ->findOrFail($this->serverId);
    }

    public function getMonitorId(string $domain): ?int
    {
        return $this->monitoredAccounts[$domain] ?? null;
    }

    public function refresh(): void
    {
        FetchServerDetailsJob::dispatch($this->server);

        Flux::toast(
            text: 'The server details will be refreshed shortly.',
            heading: 'Refreshing...',
            variant: 'success',
        );
    }

    public function saveNewApiToken(): void
    {
        $this->validate();

        $hadMissingToken = $this->server->missing_token;

        $this->server->update([
            'token' => $this->newToken,
        ]);

        if ($hadMissingToken) {
            Flux::toast(
                text: 'The server api token was added successfully.',
                heading: 'Updated...',
                variant: 'success',
            );

            $this->redirectRoute('servers.show', ['server' => $this->server->id], true, true);

            return;
        }

        Flux::toast(
            text: 'The server api token was updated successfully.',
            heading: 'Updated...',
            variant: 'success',
        );

        $this->modal('new-token-modal')->close();
    }

    public function resetApiToken(): void
    {
        $this->modal('reset-token-modal')->close();
        $this->reset('newToken');

        $this->modal('new-token-modal')->show();
    }

    public function delete(): void
    {
        $this->server->removeMonitors();
        $this->server->delete();

        $this->modal('delete-server-modal')->close();

        Flux::toast(
            text: 'The server was deleted successfully.',
            heading: 'Deleted...',
            variant: 'success',
        );

        $this->redirectRoute('servers.index', [], true, true);
    }
};
