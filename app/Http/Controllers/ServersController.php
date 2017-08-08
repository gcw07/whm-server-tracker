<?php

namespace App\Http\Controllers;

use App\Server;
use Illuminate\Http\Request;

class ServersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('servers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'name'             => ['required', 'max:191'],
            'address'          => ['required', 'max:191'],
            'port'             => ['required', 'numeric'],
            'server_type'      => ['required'],
            'notes'            => ['nullable'],
            'token'            => ['nullable'],
            'disk_used'        => ['nullable'],
            'disk_available'   => ['nullable'],
            'disk_total'       => ['nullable'],
            'disk_percentage'  => ['nullable'],
            'backup_enabled'   => ['nullable'],
            'backup_days'      => ['nullable'],
            'backup_retention' => ['nullable'],
        ]);

        $server = Server::create($data);

        return response()->json($server);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Server $server
     * @return \Illuminate\Http\Response
     */
    public function show(Server $server)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Server $server
     * @return \Illuminate\Http\Response
     */
    public function edit(Server $server)
    {
        return view('servers.edit', compact('server'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Server $server
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Server $server)
    {
        $data = $this->validate($request, [
            'name'             => ['required', 'max:191'],
            'address'          => ['required', 'max:191'],
            'port'             => ['required', 'numeric'],
            'server_type'      => ['required'],
            'notes'            => ['nullable'],
            'token'            => ['nullable'],
            'disk_used'        => ['nullable'],
            'disk_available'   => ['nullable'],
            'disk_total'       => ['nullable'],
            'disk_percentage'  => ['nullable'],
            'backup_enabled'   => ['nullable'],
            'backup_days'      => ['nullable'],
            'backup_retention' => ['nullable'],
        ]);

        $server->update($data);

        return response()->json($server);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Server $server
     * @return \Illuminate\Http\Response
     */
    public function destroy(Server $server)
    {
        //
    }
}
