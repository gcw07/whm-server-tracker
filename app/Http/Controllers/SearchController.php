<?php

namespace App\Http\Controllers;

use App\Account;
use App\Server;
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
        $term = $request->get('q');

        if ($term) {
            $servers = Server::search($term)->orderBy('name')->get();
            $accounts = Account::search($term)->orderBy('domain')->get();
        } else {
            $servers = [];
            $accounts = [];
        }

        return view('search.index', [
            'servers'  => $servers,
            'accounts' => $accounts,
        ]);
    }
}
