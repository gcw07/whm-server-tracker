@extends('layouts.master', ['menu' => 'dashboard'])

@section('title', 'Dashboard')

@section('content')

    <div class="mt-2">
        <dashboard-stats></dashboard-stats>

        <div class="columns">
            <dashboard-servers></dashboard-servers>
            <dashboard-latest-accounts></dashboard-latest-accounts>
        </div>
    </div>

@endsection