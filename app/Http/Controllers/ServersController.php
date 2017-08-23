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
        return view('servers.index');
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
            'server_type'      => ['required', 'in:dedicated,reseller,vps'],
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
            'server_type'      => ['required', 'in:dedicated,reseller,vps'],
            'notes'            => ['nullable'],
            'disk_used'        => ['nullable'],
            'disk_available'   => ['nullable'],
            'disk_total'       => ['nullable'],
            'disk_percentage'  => ['nullable'],
            'backup_enabled'   => ['nullable'],
            'backup_days'      => ['nullable'],
            'backup_retention' => ['nullable'],
        ]);

        if ($this->hasServerTypeChangedToReseller($request, $server)) {
            $data = $this->clearRemoteServerDetails($data);
        }

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
        $server->delete();

        return response([], 204);
    }

    /**
     * Has the  server type changed from dedicated or vps to reseller
     *
     * @param Request $request
     * @param Server $server
     * @return bool
     */
    private function hasServerTypeChangedToReseller(Request $request, Server $server)
    {
        return ($server->server_type !== 'reseller') && $request->get('server_type') === 'reseller';
    }

    /**
     * Clear remote server details when server type changes to a reseller
     *
     * @param $data
     * @return mixed
     */
    private function clearRemoteServerDetails($data)
    {
        $data['token'] = null;
        $data['disk_used'] = null;
        $data['disk_available'] = null;
        $data['disk_total'] = null;
        $data['disk_percentage'] = null;
        $data['backup_enabled'] = null;
        $data['backup_days'] = null;
        $data['backup_retention'] = null;

        return $data;
    }
}
