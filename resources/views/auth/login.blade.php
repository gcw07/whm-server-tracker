@extends('layouts.simple')

@section('content')
    <div class="columns">
        <div class="column is-half is-offset-one-quarter">
            <div class="box has-border-top">
                <p class="title">
                    Login
                </p>

                <hr>

                <form role="form" method="POST" action="{{ route('login') }}">
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
                        <div class="control">
                            <b-checkbox name="remember">Remember Me</b-checkbox>
                        </div>
                    </div>

                    <div class="field is-grouped">
                        <p class="control">
                            <button class="button is-primary" type="submit">
                                Login
                            </button>
                        </p>
                        <p class="control">
                            <a class="button is-link" href="{{ route('password.request') }}">
                                Forgot Your Password?
                            </a>
                        </p>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
