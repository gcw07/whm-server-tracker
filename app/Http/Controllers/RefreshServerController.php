<?php

namespace App\Http\Controllers;

use App\Jobs\FetchServerDataJob;
use App\Models\Server;

class RefreshServerController extends Controller
{
    public function update(Server $server)
    {
        FetchServerDataJob::dispatch($server);

        return response()->json(['message' => 'Server data will be refreshed shortly.']);
    }
}
