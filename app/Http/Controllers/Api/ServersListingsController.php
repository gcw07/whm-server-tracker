<?php

namespace App\Http\Controllers\Api;

use App\Filters\ServerFilters;
use App\Http\Controllers\Controller;
use App\Server;

class ServersListingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ServerFilters $filters
     * @return \Illuminate\Http\Response
     */
    public function index(ServerFilters $filters)
    {
        $servers = Server::filter($filters)->orderBy('name')->get();

        return response()->json($servers);
    }
}
