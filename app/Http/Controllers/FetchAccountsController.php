<?php

namespace App\Http\Controllers;

use App\Connectors\ServerConnector;
use App\Server;

class FetchAccountsController extends Controller
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
        $this->serverConnector->setServer($server);

        $server->fetchAccounts($this->serverConnector);

        return response()->json($server);
    }
}
