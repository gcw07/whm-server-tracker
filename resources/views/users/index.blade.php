@extends('layouts.master', ['menu' => 'users'])

@section('title', 'Users')

@section('content')

    <div>
        <div class="breadcrumb has-arrow-separator" aria-label="breadcrumbs">
            <ul>
                <li class="is-active">
                    <a href="{{ route('users.index') }}">Users</a>
                </li>
            </ul>
        </div>
    </div>

@endsection