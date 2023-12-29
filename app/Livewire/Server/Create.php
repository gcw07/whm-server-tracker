<?php

namespace App\Livewire\Server;

use App\Enums\ServerTypeEnum;
use App\Models\Server;
use Illuminate\Validation\Rules\Enum;
use Livewire\Component;

class Create extends Component
{
    /**
     * The component's state.
     */
    public array $state = [
        'name' => '',
        'address' => '',
        'port' => '',
        'server_type' => '',
        'notes' => '',
        'token' => null,
    ];

    protected $validationAttributes = [
        'state.name' => 'name',
        'state.address' => 'address',
        'state.port' => 'port',
        'state.server_type' => 'server type',
        'state.notes' => 'notes',
        'state.token' => 'token',
    ];

    public function render()
    {
        return view('livewire.server.create')->layoutData(['title' => 'Create Server']);
    }

    protected function rules(): array
    {
        return [
            'state.name' => ['required', 'string', 'max:255'],
            'state.address' => ['required', 'string', 'max:255'],
            'state.port' => ['required', 'numeric'],
            'state.server_type' => ['required', new Enum(ServerTypeEnum::class)],
            'state.notes' => ['nullable', 'string'],
            'state.token' => ['nullable', 'string'],
        ];
    }

    public function save()
    {
        $this->validate();

        $server = Server::create($this->state);

        return redirect()->route('servers.show', $server->id);
    }
}
