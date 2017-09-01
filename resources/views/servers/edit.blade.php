@extends('layouts.master', ['menu' => 'servers'])

@section('title', 'Servers')

@section('content')

    <div>
        <div class="breadcrumb has-arrow-separator" aria-label="breadcrumbs">
            <ul>
                <li><a href="{{ route('servers.index') }}">Servers</a></li>
                <li class="is-active">
                    <a href="#">{{ $server->name }}</a>
                </li>
            </ul>
        </div>
    </div>

    <servers-edit :data="{{$server}}"></servers-edit>

@endsection