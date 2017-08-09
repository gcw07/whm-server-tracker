<?php

namespace App\Connectors;

use App\Exceptions\Server\ForbiddenAccessException;
use App\Exceptions\Server\ServerConnectionException;
use App\Exceptions\Server\InvalidServerTypeException;
use App\Exceptions\Server\MissingTokenException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;

class FakeServerConnector implements ServerConnector
{
    protected $server;
    protected $timeout;

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

    public function getDiskUsage()
    {
        return [
            'used'       => 100000000,
            'available'  => 200000000,
            'total'      => 300000000,
            'percentage' => 33
        ];
    }

    public function getBackups()
    {
        return [
            'backupenable'           => 1,
            'backupdays'             => '0,2,4,6',
            'backup_daily_retention' => 10,
        ];
    }

    public function getAccounts()
    {
        return [
            [
                'domain'        => 'my-site.com',
                'user'          => 'mysite',
                'ip'            => '1.1.1.1',
                'backup'        => 1,
                'suspended'     => 0,
                'suspendreason' => 'not suspended',
                'suspendtime'   => 0,
                'startdate'     => '17 Jan 1 10:35',
                'diskused'      => '300M',
                'disklimit'     => '2000M',
                'plan'          => '2 Gig',
            ],
            [
                'domain'        => 'suspended-site.com',
                'user'          => 'suspended',
                'ip'            => '1.1.1.1',
                'backup'        => 1,
                'suspended'     => 1,
                'suspendreason' => 'Unpaid account',
                'suspendtime'   => 1501734198,
                'startdate'     => '16 Jun 13 10:53',
                'diskused'      => '500M',
                'disklimit'     => '4000M',
                'plan'          => '4 Gig',
            ],
            [
                'domain'        => 'gwscripts.com',
                'user'          => 'gwscripts',
                'ip'            => '1.1.1.1',
                'backup'        => 1,
                'suspended'     => 1,
                'suspendreason' => 'Unknown',
                'suspendtime'   => 1501734198,
                'startdate'     => '17 May 19 10:45',
                'diskused'      => '300M',
                'disklimit'     => '2000M',
                'plan'          => '2 Gig',
            ]
        ];
    }

    public function getSystemLoadAvg()
    {

    }
}
