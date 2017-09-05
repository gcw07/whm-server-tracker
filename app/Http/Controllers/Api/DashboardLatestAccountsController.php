<?php

namespace App\Http\Controllers\Api;

use App\Account;
use App\Http\Controllers\Controller;

class DashboardLatestAccountsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $accounts = Account::with('server')->latest()->take(5)->get();

        return response()->json($accounts);
    }
}
