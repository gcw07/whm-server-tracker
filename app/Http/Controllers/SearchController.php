<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Server;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($term = $request->get('q')) {
            $servers = Server::search($term)->orderBy('name')->get();
            $accounts = Account::with('server')->search($term)->orderBy('domain')->get();
        } else {
            $servers = [];
            $accounts = [];
        }

//        return view('search.index', [
//            'servers'  => $servers,
//            'accounts' => $accounts,
//        ]);
    }
}
