@extends('layouts.master', ['menu' => 'accounts'])

@section('title', 'Accounts')

@section('content')

    <div>
        <div class="breadcrumb has-arrow-separator" aria-label="breadcrumbs">
            <ul>
            @if ($server)
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
            @endif
            </ul>
        </div>
    </div>

    @if ($server)
        <accounts-listing :id="{{ $server->id }}"></accounts-listing>
    @else
        <accounts-listing></accounts-listing>
    @endif
@endsection