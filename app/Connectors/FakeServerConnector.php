<?php

namespace App\Connectors;

use App\Exceptions\Server\InvalidServerTypeException;
use App\Exceptions\Server\MissingTokenException;

class FakeServerConnector implements ServerConnector
{
    protected $server;
    protected $timeout;
    protected $accounts;

    public function __construct()
    {
        $this->accounts = [
            [
                'domain' => 'my-site.com',
                'user' => 'mysite',
                'ip' => '1.1.1.1',
                'backup' => 1,
                'suspended' => 0,
                'suspendreason' => 'not suspended',
                'suspendtime' => 0,
                'startdate' => '17 Jan 1 10:35',
                'diskused' => '300M',
                'disklimit' => '2000M',
                'plan' => '2 Gig',
            ],
        ];
    }

    public function setServer($server)
    {
        $this->server = $server;

        if ($this->server->server_type === 'reseller') {
            throw new InvalidServerTypeException;
        }

        if (! $this->server->token) {
            throw new MissingTokenException;
        }
    }

    public function setTimeout($seconds)
    {
        $this->timeout = $seconds;
    }

    public function setAccounts($accounts)
    {
        $this->accounts = $accounts;
    }

    public function getDiskUsage()
    {
        return [
            'used' => 100000000,
            'available' => 200000000,
            'total' => 300000000,
            'percentage' => 33,
        ];
    }

    public function getBackups()
    {
        return [
            'backupenable' => 1,
            'backupdays' => '0,2,4,6',
            'backup_daily_retention' => 10,
        ];
    }

    public function getPhpDefaultVersion()
    {
        return 'ea-php70';
    }

    public function getPhpVersions()
    {
        return 'ea-php70';
    }

    public function getAccounts()
    {
        return $this->accounts;
    }

    public function getSystemLoadAvg()
    {
    }
}
