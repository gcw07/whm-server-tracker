<?php

namespace App\Http\Controllers;

use App\Connectors\ServerConnector;
use App\Exceptions\Server\ForbiddenAccessException;
use App\Exceptions\Server\InvalidServerTypeException;
use App\Exceptions\Server\MissingTokenException;
use App\Exceptions\Server\ServerConnectionException;
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
        try {
            $this->serverConnector->setServer($server);

            $server->fetchDiskUsageDetails($this->serverConnector);
            $server->fetchBackupDetails($this->serverConnector);

            $server->update([
                'details_last_updated' => Carbon::now()
            ]);
        } catch (InvalidServerTypeException $e) {
            return response()->json(['message' => 'Server type must be a vps or dedicated server.'], 422);
        } catch (MissingTokenException $e) {
            return response()->json(['message' => 'Server API token is missing.'], 422);
        } catch (ServerConnectionException $e) {
            return response()->json(['message' => 'Unable to connect to server. Try again later.'], 422);
        } catch (ForbiddenAccessException $e) {
            return response()->json(['message' => 'Access if forbidden on server. Check credentials.'], 422);
        }

        return response()->json($server);
    }
}
