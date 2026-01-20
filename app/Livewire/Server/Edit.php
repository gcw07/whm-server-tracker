<?php

namespace App\Livewire\Server;

use App\Enums\ServerTypeEnum;
use App\Models\Server;
use Illuminate\Validation\Rules\Enum;
use Livewire\Component;
use Usernotnull\Toast\Concerns\WireToast;

class Edit extends Component
{
    use WireToast;

    public Server $server;

    /**
     * The component's state.
     */
    public array $state = [];

    protected $validationAttributes = [
        'state.name' => 'name',
        'state.address' => 'address',
        'state.port' => 'port',
        'state.server_type' => 'server type',
        'state.notes' => 'notes',
    ];

    public function mount(Server $server): void
    {
        $this->server = $server;

        $this->state = $server->only(['name', 'address', 'port', 'server_type', 'notes']);
    }

    public function render()
    {
        return view('livewire.server.edit')->layoutData(['title' => 'Edit Server']);
    }

    protected function rules(): array
    {
        return [
            'state.name' => ['required', 'string', 'max:255'],
            'state.address' => ['required', 'string', 'max:255'],
            'state.port' => ['required', 'numeric'],
            'state.server_type' => ['required', new Enum(ServerTypeEnum::class)],
            'state.notes' => ['nullable', 'string'],
        ];
    }

    public function save(): \Livewire\Features\SupportRedirects\Redirector | \Illuminate\Http\RedirectResponse
    {
        $this->validate();

        $this->server->update($this->state);

        toast()->success('The server information was updated successfully.')->pushOnNextPage();

        return to_route('servers.show', $this->server);
    }
}
