<?php

namespace App\Services\Blacklist\Drivers;

use App\Services\Blacklist\BlacklistResult;
use App\Services\Blacklist\Contracts\BlacklistDriver;

class SpamhausDBLDriver implements BlacklistDriver
{
    public function name(): string
    {
        return 'Spamhaus DBL';
    }

    public function check(string $domain, ?string $ip): BlacklistResult
    {
        if ($this->isListed("{$domain}.dbl.spamhaus.org.")) {
            return BlacklistResult::listed($this->name(), 'Listed on dbl.spamhaus.org');
        }

        return BlacklistResult::clean($this->name());
    }

    protected function isListed(string $lookup): bool
    {
        return checkdnsrr($lookup, 'A');
    }
}
