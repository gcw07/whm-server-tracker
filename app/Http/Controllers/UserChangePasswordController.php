<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserChangePasswordController extends Controller
{
    /**
     * @param Request $request
     * @param User $user
     * @return Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, User $user): Response
    {
        $this->validate($request, [
            'password' => ['required', 'string', 'min:6', 'confirmed']
        ]);

        $user->update(['password' => bcrypt($request->get('password'))]);

        return response([], 204);
    }
}
