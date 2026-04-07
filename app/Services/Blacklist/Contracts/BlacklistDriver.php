<?php

namespace App\Services\Blacklist\Contracts;

use App\Services\Blacklist\BlacklistResult;

interface BlacklistDriver
{
    public function name(): string;

    public function url(): string;

    public function check(string $domain, ?string $ip): BlacklistResult;
}
