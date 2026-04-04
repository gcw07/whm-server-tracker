<?php

namespace App\Services\Blacklist;

final readonly class BlacklistResult
{
    public function __construct(
        public string $driver,
        public bool $listed,
        public ?string $reason = null,
    ) {}

    public static function clean(string $driver): self
    {
        return new self($driver, false);
    }

    public static function listed(string $driver, string $reason): self
    {
        return new self($driver, true, $reason);
    }
}
