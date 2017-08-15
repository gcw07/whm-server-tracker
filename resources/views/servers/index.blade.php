@extends('layouts.master', ['menu' => 'servers'])

@section('title', 'Servers')

@section('content')

    <div>
        <div class="breadcrumb has-arrow-separator" aria-label="breadcrumbs">
            <ul>
                <li class="is-active">
                    <a href="{{ route('servers.index') }}">Servers</a>
                </li>
            </ul>
        </div>
    </div>

    <servers-listing></servers-listing>

@endsection