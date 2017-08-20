<?php

namespace App\Http\Controllers;

use App\Account;
use App\Server;
use Illuminate\Http\Request;

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
        return view('accounts.index', $server);
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
