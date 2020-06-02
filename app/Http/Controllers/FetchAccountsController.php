<?php

namespace App\Http\Controllers;

use App\Jobs\FetchServerAccounts;
use App\Models\Server;

class FetchAccountsController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Server $server
     * @return \Illuminate\Http\Response
     */
    public function update(Server $server)
    {
        FetchServerAccounts::dispatch($server);

        return response()->json(['message' => 'Server accounts will be refreshed shortly.']);
    }
}
