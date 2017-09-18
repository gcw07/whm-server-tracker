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

    <search-servers :data="{{ $servers }}"></search-servers>

    <search-accounts :data="{{ $accounts }}"></search-accounts>

@endsection