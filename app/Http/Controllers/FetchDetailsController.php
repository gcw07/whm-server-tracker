<?php

namespace App\Http\Controllers;

use App\Jobs\FetchServerDetails;
use App\Models\Server;

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
        FetchServerDetails::dispatch($server);

        return response()->json(['message' => 'Server details will be refreshed shortly.']);
    }
}
