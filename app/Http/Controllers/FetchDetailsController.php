<?php

namespace App\Http\Controllers;

use App\Server;

class FetchDetailsController extends Controller
{
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
