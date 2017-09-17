@extends('layouts.master', ['menu' => 'search'])

@section('title', 'Search')

@section('content')

    <div>
        <div class="breadcrumb has-arrow-separator" aria-label="breadcrumbs">
            <ul>
                <li class="is-active">
                    <a href="{{ route('search') }}">Search</a>
                </li>
            </ul>
        </div>
    </div>

    @forelse($servers as $server)
        <p>{{ $server->name }}</p>
    @empty
        <p>no server results</p>
    @endforelse

    <hr>

    @forelse($accounts as $account)
        <p>{{ $account->domain }}</p>
    @empty
        <p>no account results</p>
    @endforelse

@endsection