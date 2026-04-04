<?php

namespace App\Services\Blacklist\Drivers;

use App\Services\Blacklist\BlacklistResult;
use App\Services\Blacklist\Contracts\BlacklistDriver;

class MultiSurblDriver implements BlacklistDriver
{
    public function name(): string
    {
        return 'SURBL';
    }

    public function check(string $domain, ?string $ip): BlacklistResult
    {
        if ($this->isListed("{$domain}.multi.surbl.org.")) {
            return BlacklistResult::listed($this->name(), 'Listed on multi.surbl.org');
        }

        return BlacklistResult::clean($this->name());
    }

    protected function isListed(string $lookup): bool
    {
        return checkdnsrr($lookup, 'A');
    }
}
