<?php

namespace App\Http\Controllers;

use App\Server;
use Illuminate\Http\Request;

class ServersTokenController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Server $server
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Server $server)
    {
        $data = $this->validate($request, [
            'token' => ['required', 'max:191'],
        ]);

        $server->update($data);

        return response()->json($server);
    }
}
