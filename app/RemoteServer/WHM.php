<?php

namespace App\RemoteServer;

use App\Backups;
use App\DiskUsage;
use App\Exceptions\InvalidServerTypeException;
use App\Exceptions\MissingTokenException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class WHM
{
    protected $authHeader;
    protected $baseUrl;
    protected $server;

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

    public function getDiskUsage()
    {
        $url = "{$this->baseUrl}/getdiskusage?api.version=1";

        return $this->fetch($url);
    }

    private function setupConnection()
    {
        $config = config('server-tracker');
        $host = "{$this->server->address}:{$this->server->port}";

        $this->baseUrl = "{$config['urls']['protocol']}://{$host}/{$config['urls']['api-path']}";
        $this->authHeader = [
            'Authorization' => "whm {$config['remote']['username']}:{$this->server->token}"
        ];
    }

    private function fetch($url)
    {
        try {
            $client = new Client();

            $response = $client->request('GET', $url, [
                'headers' => $this->authHeader,
                'verify' => false
            ]);

            return json_decode($response->getBody(), true);
        } catch (ClientException $e) {
            dd('remote server error');
        }
    }
}
