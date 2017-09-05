<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Server;

class DashboardServersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $servers = Server::all();

        $counts = $servers->groupBy('server_type')->map->count();

        return response()->json($counts);
    }
}
