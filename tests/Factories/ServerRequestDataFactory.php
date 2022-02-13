<?php

namespace Tests\Factories;

use App\Enums\ServerTypeEnum;

class ServerRequestDataFactory
{
    protected string $name = 'my-server-name';
    protected string $address = '127.0.0.1';
    protected string $port = '2087';
    protected ServerTypeEnum $serverType;
    protected string $notes = 'a server note';
    protected string $token = 'server-api-token';

    public static function new(): self
    {
        return new self();
    }

    public function __construct()
    {
        $this->serverType = ServerTypeEnum::Vps;
    }

    public function withName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function withAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function withPort(string $port): self
    {
        $this->port = $port;

        return $this;
    }

    public function withServerType(ServerTypeEnum $serverType): self
    {
        $this->serverType = $serverType;

        return $this;
    }

    public function withNotes(string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function withToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function create(array $overrides = []): array
    {
        return array_merge([
            'name' => $this->name,
            'address' => $this->address,
            'port' => $this->port,
            'server_type' => $this->serverType,
            'notes' => $this->notes,
            'token' => $this->token,
        ], $overrides);
    }
}
