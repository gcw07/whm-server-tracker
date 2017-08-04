<?php

namespace App\RemoteServer;

use App\Exceptions\InvalidServerTypeException;
use App\Exceptions\MissingTokenException;
use Zttp\Zttp;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class WHM
{
    protected $baseUrl;
    protected $header;
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

        /////////////////////////////////////
//        $response = Zttp::withHeaders([
//            'Authorization' => "whm root:XAT5VTR67T5"
//        ])->withoutVerifying()->get($url);
//
//        dd($response->json());

        //////////////////////////
        $client = new Client();
        $response = $client->request('GET', $url, [
            'headers' => [
                'Authorization' => "whm root:XAT5VTR67T5",
            ],
            'verify' => false
        ]);
        $data = json_decode($response->getBody(), true);
        dd($data);

        ////////////////////////
        $client = new Client();
        $request = new Request('GET', $url, [
            'Authorization' => "whm root:XAT5VTR67T5"
        ]);
        dd($request);
        $response = $client->send($request, ['verify' => false]);
        $data = json_decode($response->getBody(), true);
        dd($data);
        /////////////////////////
    }

    private function setupConnection()
    {
        $config = config('server-tracker');

        $host = "{$this->server->address}:{$this->server->port}";

        $this->baseUrl = "{$config['urls']['protocol']}://{$host}/{$config['urls']['api-path']}";

        $this->header = "Authorization: whm {$config['remote']['username']}:{$this->server->token}";

//        $this->header = [
//            'Authorization' => "whm {$config['remote']['username']}:{$this->server->token}"
//        ];

    }
}
