<?php

namespace App\Services\Blacklist\Drivers;

use App\Services\Blacklist\BlacklistResult;
use App\Services\Blacklist\Contracts\BlacklistDriver;

class SpamhausZenDriver implements BlacklistDriver
{
    public function name(): string
    {
        return 'Spamhaus ZEN';
    }

    public function check(string $domain, ?string $ip): BlacklistResult
    {
        if (! $ip) {
            return BlacklistResult::clean($this->name());
        }

        $reversed = implode('.', array_reverse(explode('.', $ip)));

        if ($this->isListed("{$reversed}.zen.spamhaus.org.")) {
            return BlacklistResult::listed($this->name(), 'Listed on zen.spamhaus.org');
        }

        return BlacklistResult::clean($this->name());
    }

    protected function isListed(string $lookup): bool
    {
        return checkdnsrr($lookup, 'A');
    }
}
