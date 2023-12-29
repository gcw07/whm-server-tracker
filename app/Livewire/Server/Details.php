<?php

namespace App\Livewire\Server;

use App\Jobs\FetchServerDataJob;
use App\Models\Server;
use Livewire\Component;
use Usernotnull\Toast\Concerns\WireToast;

class Details extends Component
{
    use WireToast;

    public Server $server;

    public function mount(Server $server): void
    {
        $server->loadMissing(['accounts' => function ($query) {
            return $query->orderBy('domain');
        }, 'accounts.server'])->loadCount(['accounts']);

        $this->server = $server;
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
