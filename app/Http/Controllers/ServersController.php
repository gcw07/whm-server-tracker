<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateServerRequest;
use App\Http\Requests\UpdateServerRequest;
use App\Models\Server;
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

    public function create()
    {

    }

    public function store(CreateServerRequest $request)
    {
        $data = collect($request->validated())->merge([
            'settings' => []
        ])->toArray();

        $server = Server::create($data);

        return redirect()->route('servers.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Server $server
     * @return \Illuminate\Http\Response
     */
    public function show(Server $server)
    {
        return view('servers.show', compact('server'));
    }

    public function edit(Server $server)
    {
//        return view('servers.edit', compact('server'));
    }

    public function update(UpdateServerRequest $request, Server $server)
    {
        $data = $request->validated();

//        if ($this->hasServerTypeChangedToReseller($request, $server)) {
//            $data = $this->clearRemoteServerDetails($data);
//        }

        $server->update($data);

        return redirect()->route('servers.index');
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
        $data['settings'] = [];

        return $data;
    }
}
