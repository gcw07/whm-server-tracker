<?php

namespace App\Http\Controllers\Api;

use App\Account;
use App\Http\Controllers\Controller;
use App\Server;
use App\User;

class DashboardStatsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $accounts = Account::count();
        $servers = Server::count();
        $users = User::count();

        $data = [
            'accounts' => $accounts,
            'servers' => $servers,
            'users' => $users
        ];

        return response()->json($data);
    }
}
