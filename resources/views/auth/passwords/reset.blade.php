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

                <form role="form" method="POST" action="{{ route('password.request') }}">
                    {{ csrf_field() }}

                    <input type="hidden" name="token" value="{{ $token }}">

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
                        <label class="label" for="password">Password</label>
                        <p class="control has-icons-left">
                            <input id="password" type="password" class="input{{ $errors->has('password') ? ' is-danger' : '' }}" name="password" required>
                            <span class="icon is-small is-left">
                                <i class="fa fa-lock"></i>
                            </span>
                            @if ($errors->has('password'))
                                <p class="help is-danger">{{ $errors->first('password') }}</p>
                            @endif
                        </p>
                    </div>

                    <div class="field">
                        <label class="label" for="password-confirm">Confirm Password</label>
                        <p class="control has-icons-left">
                            <input id="password-confirm" type="password" class="input{{ $errors->has('password_confirmation') ? ' is-danger' : '' }}" name="password_confirmation" required>
                            <span class="icon is-small is-left">
                                <i class="fa fa-lock"></i>
                            </span>
                            @if ($errors->has('password_confirmation'))
                                <p class="help is-danger">{{ $errors->first('password_confirmation') }}</p>
                            @endif
                        </p>
                    </div>

                    <div class="field">
                        <p class="control">
                            <button class="button is-primary" type="submit">
                                Reset Password
                            </button>
                        </p>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
