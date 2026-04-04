<?php

namespace App\Services\Blacklist\Drivers;

use App\Services\Blacklist\BlacklistResult;
use App\Services\Blacklist\Contracts\BlacklistDriver;

class BarracudaCentralDriver implements BlacklistDriver
{
    public function name(): string
    {
        return 'Barracuda Central';
    }

    public function check(string $domain, ?string $ip): BlacklistResult
    {
        if (! $ip) {
            return BlacklistResult::clean($this->name());
        }

        $reversed = implode('.', array_reverse(explode('.', $ip)));

        if ($this->isListed("{$reversed}.b.barracudacentral.org.")) {
            return BlacklistResult::listed($this->name(), 'Listed on b.barracudacentral.org');
        }

        return BlacklistResult::clean($this->name());
    }

    protected function isListed(string $lookup): bool
    {
        return checkdnsrr($lookup, 'A');
    }
}
