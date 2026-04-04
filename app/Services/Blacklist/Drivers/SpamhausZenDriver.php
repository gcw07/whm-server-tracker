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
        $records = dns_get_record($lookup, DNS_A);

        if (empty($records)) {
            return false;
        }

        // Only treat as listed for SBL and XBL — real spam/exploit listings.
        // Ignore PBL (127.0.0.10, 127.0.0.11, 127.0.0.14) — policy list, normal for hosting IPs.
        // Ignore rate-limit responses (127.255.255.254, 127.255.255.255).
        $listingCodes = [
            '127.0.0.2', // SBL
            '127.0.0.3', // SBL CSS
            '127.0.0.4', // XBL
            '127.0.0.5', // XBL
            '127.0.0.6', // XBL
            '127.0.0.7', // XBL
            '127.0.0.9', // SBL DROP/EDROP
        ];

        foreach ($records as $record) {
            if (in_array($record['ip'], $listingCodes, true)) {
                return true;
            }
        }

        return false;
    }
}
