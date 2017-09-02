<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UsersChangePasswordController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $this->validate($request, [
            'password' => ['required', 'string', 'min:6', 'confirmed']
        ]);

        $user->update(['password' => bcrypt($request->get('password'))]);

        return response([], 204);
    }
}
