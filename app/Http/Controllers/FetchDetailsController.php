<?php

namespace App\Http\Controllers;

use App\Connectors\ServerConnector;
use App\Server;

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
        $server->fetchDiskUsageDetails();
        $server->fetchBackupDetails();

        return response()->json($server);
    }
}
