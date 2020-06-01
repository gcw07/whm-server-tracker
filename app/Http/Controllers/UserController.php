<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        return view('users.index');
    }

    public function create()
    {

    }

    public function store(CreateUserRequest $request)
    {
        $data = collect($request->validated())->merge([
            'password' => bcrypt($request->get('password'))
        ])->toArray();

        User::create($data);

        return redirect()->route('users.index');
    }

    public function edit(User $user)
    {
//        return view('users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update($request->validated());

        return redirect()->route('users.index');

//        return response()->json($user);
    }

    public function destroy(User $user): Response
    {
        if ($user->id === auth()->user()->id) {
            return response(['message' => 'You may not delete yourself.'], 422);
        }

        $user->delete();

        return response([], 204);
    }
}
