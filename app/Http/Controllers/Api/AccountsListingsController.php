<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Account;

class AccountsListingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $accounts = Account::all();

        return response()->json($accounts);
    }
}
