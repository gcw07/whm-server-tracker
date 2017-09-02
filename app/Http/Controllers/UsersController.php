<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('users.index');
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
            'name'     => ['required', 'string', 'max:191'],
            'email'    => ['required', 'string', 'email', 'max:191', Rule::unique('users')],
            'password' => ['required', 'string', 'min:6', 'confirmed']
        ]);

        $data['password'] = bcrypt($data['password']);

        $user = User::create($data);

        return response()->json($user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $data = $this->validate($request, [
            'name'  => ['required', 'string', 'max:191'],
            'email' => ['required', 'string', 'email', 'max:191', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->update($data);

        return response()->json($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->user()->id) {
            return response(['message' => 'You may not delete yourself.'], 422);
        }

        $user->delete();

        return response([], 204);
    }
}
