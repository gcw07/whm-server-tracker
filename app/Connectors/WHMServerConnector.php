<?php

namespace App\Connectors;

use App\Exceptions\Server\ForbiddenAccessException;
use App\Exceptions\Server\ServerConnectionException;
use App\Exceptions\Server\InvalidServerTypeException;
use App\Exceptions\Server\MissingTokenException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;

class WHMServerConnector implements ServerConnector
{
    protected $authHeader;
    protected $baseUrl;
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

        $this->setupConnection();
    }

    public function setTimeout($seconds)
    {
        $this->timeout = $seconds;
    }

    public function getDiskUsage()
    {
        $data = $this->fetch("{$this->baseUrl}/getdiskusage?api.version=1");

        if (array_key_exists('data', $data)) {
            return $data['data']['partition'][0];
        }

        return false;
    }

    public function getBackups()
    {
        $data = $this->fetch("{$this->baseUrl}/backup_config_get?api.version=1");

        if (array_key_exists('data', $data)) {
            return $data['data']['backup_config'];
        }

        return false;
    }

    public function getPhpVersion()
    {
        $data = $this->fetch("{$this->baseUrl}/php_get_system_default_version?api.version=1");

        if (array_key_exists('data', $data)) {
            return $data['data']['version'];
        }

        return false;
    }

    public function getAccounts()
    {
        $data = $this->fetch("{$this->baseUrl}/listaccts?api.version=1");

        if (array_key_exists('data', $data)) {
            return $data['data']['acct'];
        }

        return false;
    }

    public function getSystemLoadAvg()
    {
        $data = $this->fetch("{$this->baseUrl}/systemloadavg?api.version=1");

        if (array_key_exists('data', $data)) {
            return $data['data'];
        }

        return false;
    }

    private function setupConnection()
    {
        $config = config('server-tracker');
        $host = "{$this->server->address}:{$this->server->port}";

        $this->baseUrl = "{$config['urls']['protocol']}://{$host}/{$config['urls']['api-path']}";
        $this->authHeader = [
            'Authorization' => "whm {$config['remote']['username']}:{$this->server->token}"
        ];
        $this->timeout = $config['remote']['timeout'];
    }

    private function fetch($url)
    {
        try {
            $client = new Client();

            $response = $client->request('GET', $url, [
                'headers' => $this->authHeader,
                'verify'  => false,
                'connect_timeout' => $this->timeout
            ]);

            return json_decode($response->getBody(), true);
        } catch (ConnectException $e) {
            throw new ServerConnectionException;
        } catch (ClientException $e) {
            throw new ForbiddenAccessException;
        }
    }
}
