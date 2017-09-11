<?php

namespace App\Http\Controllers;

use App\Connectors\ServerConnector;
use App\Exceptions\Server\ForbiddenAccessException;
use App\Exceptions\Server\InvalidServerTypeException;
use App\Exceptions\Server\MissingTokenException;
use App\Exceptions\Server\ServerConnectionException;
use App\Jobs\FetchServerDetails;
use App\Server;
use Carbon\Carbon;

class FetchDetailsController extends Controller
{
    private $serverConnector;

    public function __construct(ServerConnector $serverConnector)
    {
        $this->serverConnector = $serverConnector;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Server $server
     * @return \Illuminate\Http\Response
     */
    public function update(Server $server)
    {
        FetchServerDetails::dispatch($server);

        return response()->json(['message' => 'Server details will be refreshed shortly.']);
    }
}
