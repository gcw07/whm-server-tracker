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
        $records = dns_get_record($lookup, DNS_A);

        if (empty($records)) {
            return false;
        }

        // Only treat as listed for actual spam/phishing/malware codes.
        // Ignore 127.0.255.254 (test) and 127.0.255.255 (rate limited / invalid query).
        $listingCodes = [
            '127.0.1.2',   // spam domain
            '127.0.1.4',   // phishing domain
            '127.0.1.5',   // malware domain
            '127.0.1.6',   // botnet C&C
            '127.0.1.102', // abused legit spam
            '127.0.1.103', // abused spammed redirector
            '127.0.1.104', // abused legit phishing
            '127.0.1.105', // abused legit malware
            '127.0.1.106', // abused legit botnet C&C
        ];

        foreach ($records as $record) {
            if (in_array($record['ip'], $listingCodes, true)) {
                return true;
            }
        }

        return false;
    }
}
