<?php

namespace App\Livewire\Forms;

use App\Enums\ServerTypeEnum;
use App\Models\Server;
use Illuminate\Validation\Rules\Enum;
use Livewire\Form;

class EditServerForm extends Form
{
    public ?Server $server;

    public string $name = '';

    public string $address = '';

    public $port = '';

    public $serverType = '';

    public string $hostingProvider = '';

    public $notes = '';

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'port' => ['required', 'numeric'],
            'serverType' => ['required', new Enum(ServerTypeEnum::class)],
            'hostingProvider' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function setServer(Server $server): void
    {
        $this->server = $server;

        $this->name = $server->name;
        $this->address = $server->address;
        $this->port = $server->port;
        $this->serverType = $server->server_type;
        $this->hostingProvider = $server->hosting_provider ?? '';
        $this->notes = $server->notes;
    }

    public function store()
    {
        $this->validate();

        return $this->server->update([
            'name' => $this->name,
            'address' => $this->address,
            'port' => $this->port,
            'server_type' => $this->serverType,
            'hosting_provider' => $this->hostingProvider,
            'notes' => $this->notes,
        ]);
    }
}
