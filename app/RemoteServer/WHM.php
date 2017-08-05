<?php

namespace App\RemoteServer;

use App\Backups;
use App\DiskUsage;
use App\Exceptions\Server\ForbiddenAccessException;
use App\Exceptions\Server\ServerConnectionException;
use App\Exceptions\Server\InvalidServerTypeException;
use App\Exceptions\Server\MissingTokenException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;

class WHM
{
    protected $authHeader;
    protected $baseUrl;
    protected $server;
    protected $timeout;

    public function __construct($server)
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

    public static function create($server)
    {
        return new static($server);
    }

    public function setTimeout($seconds)
    {
        $this->timeout = $seconds;
    }

    public function getDiskUsage()
    {
        $url = "{$this->baseUrl}/getdiskusage?api.version=1";
        $data = $this->fetch($url);
        return new DiskUsage($data);
    }

    public function getBackups()
    {
        $url = "{$this->baseUrl}/backup_config_get?api.version=1";
        $data = $this->fetch($url);
        return new Backups($data);
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
