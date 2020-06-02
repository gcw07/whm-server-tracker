<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Server;

class DashboardServersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $servers = collect(Server::all()->toArray());

        $counts = $servers->groupBy('server_type')->map->count();

        return response()->json($counts);
    }
}
