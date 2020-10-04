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
        $counts = [
            'dedicated' => Server::where('server_type', 'dedicated')->count(),
            'reseller' => Server::where('server_type', 'reseller')->count(),
            'vps' => Server::where('server_type', 'vps')->count(),
        ];

        return response()->json($counts);
    }
}
