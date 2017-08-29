@extends('layouts.master', ['menu' => 'users'])

@section('title', 'Users')

@section('content')

    <div>
        <div class="breadcrumb has-arrow-separator" aria-label="breadcrumbs">
            <ul>
                <li><a href="{{ route('users.index') }}">Users</a></li>
                <li class="is-active">
                    <a href="#">{{ $user->name }}</a>
                </li>
            </ul>
        </div>
    </div>

@endsection