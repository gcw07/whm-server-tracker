@extends('layouts.master', ['menu' => 'accounts'])

@section('title', 'Accounts')

@section('content')

    <div>
        <div class="breadcrumb has-arrow-separator" aria-label="breadcrumbs">
            <ul>
            @isset ($server)
                <li>
                    <a href="{{ route('accounts.index') }}">Accounts</a>
                </li>
                <li class="is-active">
                    <a href="{{ route('accounts.server-index', $server) }}">{{ $server->name }}</a>
                </li>
            @else
                <li class="is-active">
                    <a href="{{ route('accounts.index') }}">Accounts</a>
                </li>
            @endisset
            </ul>
        </div>
    </div>

    @isset ($server)
        <accounts-listing :data="{{ $server }}"></accounts-listing>
    @else
        <accounts-listing></accounts-listing>
    @endisset
@endsection