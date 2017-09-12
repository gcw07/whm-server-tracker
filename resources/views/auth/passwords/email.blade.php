@extends('layouts.simple')

@section('content')
    <div class="columns">
        <div class="column is-half is-offset-one-quarter">
            <div class="box has-border-top">
                <p class="title">
                    Reset Password
                </p>

                <hr>

                @if (session('status'))
                    <div class="notification is-success">
                        {{ session('status') }}
                    </div>
                @endif

                <form role="form" method="POST" action="{{ route('password.email') }}">
                    {{ csrf_field() }}

                    <div class="field">
                        <label class="label" for="email">E-Mail Address</label>
                        <p class="control has-icons-left">
                            <input id="email" type="email" class="input{{ $errors->has('email') ? ' is-danger' : '' }}" name="email" value="{{ old('email') }}" required autofocus>
                            <span class="icon is-small is-left">
                                <i class="fa fa-envelope"></i>
                            </span>
                            @if ($errors->has('email'))
                                <p class="help is-danger">{{ $errors->first('email') }}</p>
                            @endif
                        </p>
                    </div>

                    <div class="field">
                        <p class="control">
                            <button class="button is-primary" type="submit">
                                Send Password Reset Link
                            </button>
                        </p>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
