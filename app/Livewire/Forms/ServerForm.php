<?php

namespace App\Livewire\Forms;

use App\Enums\ServerTypeEnum;
use App\Models\Server;
use Illuminate\Validation\Rules\Enum;
use Livewire\Form;

class ServerForm extends Form
{
    public string $name = '';

    public string $address = '';

    public $port = '2087';

    public $serverType = '';

    public $notes = '';

    public $token = '';

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'port' => ['required', 'numeric'],
            'serverType' => ['required', new Enum(ServerTypeEnum::class)],
            'notes' => ['nullable', 'string'],
            'token' => ['nullable', 'string'],
        ];
    }

    public function store()
    {
        $this->validate();

        return Server::create([
            'name'        => $this->name,
            'address'     => $this->address,
            'port'        => $this->port,
            'server_type' => $this->serverType,
            'notes'       => $this->notes,
            'token'       => $this->token,
        ]);
    }
}
