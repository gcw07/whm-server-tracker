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
            'backupenable' => 1,
            'backupdays' => '0,2,4,6',
            'backup_daily_retention' => 10,
        ];
    }

    public function getAccounts()
    {

    }

    public function getSystemLoadAvg()
    {

    }
}
