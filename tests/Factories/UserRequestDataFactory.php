<?php

namespace Tests\Factories;

class UserRequestDataFactory
{
    protected string $name = 'Grant Williams';
    protected string $email = 'grant@example.com';
    protected string $password = 'secret';

    public static function new(): self
    {
        return new self();
    }

    public function withName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function withEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function withPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function create(array $overrides = []): array
    {
        return array_merge([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ], $overrides);
    }
}
