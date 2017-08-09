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
        return ['fake' => 'disk usage'];
    }

    public function getBackups()
    {

    }

    public function getAccounts()
    {

    }

    public function getSystemLoadAvg()
    {

    }
}
