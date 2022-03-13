<?php

namespace App\Services\WHM;

class WhmAuth
{
    public function __construct(
        private string $protocol,
        private string $username,
        private string $timeout,
    ) {}

    public function url($host, $function): string
    {
        return "$this->protocol://$host/json-api/$function";
    }

    public function authorization($token): string
    {
        return "whm $this->username:$token";
    }

    public function timeout(): string
    {
        return $this->timeout;
    }
}
