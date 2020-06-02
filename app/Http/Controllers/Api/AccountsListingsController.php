<?php

namespace App\Http\Controllers\Api;

use App\Filters\AccountFilters;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Server;

class AccountsListingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Server $server
     * @param AccountFilters $filters
     * @return \Illuminate\Http\Response
     */
    public function index(Server $server, AccountFilters $filters)
    {
        $accounts = Account::with('server')->filter($filters)->orderBy('domain');

        if ($server->exists) {
            $accounts->where('server_id', $server->id);
        }

        $accounts = $accounts->get();

        return response()->json($accounts);
    }
}
