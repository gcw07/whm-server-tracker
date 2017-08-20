@extends('layouts.master', ['menu' => 'accounts'])

@section('title', 'Accounts')

@section('content')

    <div>
        <div class="breadcrumb has-arrow-separator" aria-label="breadcrumbs">
            <ul>
                <li class="is-active">
                    <a href="{{ route('accounts.index') }}">Accounts</a>
                </li>
            </ul>
        </div>
    </div>



@endsection