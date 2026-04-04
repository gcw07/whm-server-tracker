<?php

namespace App\Services\Blacklist\Drivers;

use App\Services\Blacklist\BlacklistResult;
use App\Services\Blacklist\Contracts\BlacklistDriver;

class SpamCopDriver implements BlacklistDriver
{
    public function name(): string
    {
        return 'SpamCop';
    }

    public function check(string $domain, ?string $ip): BlacklistResult
    {
        if (! $ip) {
            return BlacklistResult::clean($this->name());
        }

        $reversed = implode('.', array_reverse(explode('.', $ip)));

        if ($this->isListed("{$reversed}.bl.spamcop.net.")) {
            return BlacklistResult::listed($this->name(), 'Listed on bl.spamcop.net');
        }

        return BlacklistResult::clean($this->name());
    }

    protected function isListed(string $lookup): bool
    {
        return checkdnsrr($lookup, 'A');
    }
}
