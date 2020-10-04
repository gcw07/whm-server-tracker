<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Server;

class AccountsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Server $server
     * @return \Illuminate\Http\Response
     */
    public function index(Server $server)
    {
        if ($server->exists) {
//            return view('accounts.index', compact('server'));
        }

        return view('accounts.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function show(Account $account)
    {
        //
    }
}
