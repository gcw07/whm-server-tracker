<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Server;
use Illuminate\Http\Request;

class ServersListingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $servers = Server::all();

        return response()->json($servers);
    }
}
