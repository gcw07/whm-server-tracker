<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Server Tracker') }} - @yield('title')</title>

    <!-- Styles -->
    <link rel="icon" href="data:;base64,iVBORwOKGO=">
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">

    <!-- Scripts -->
    <script defer src="{{ mix('js/fontawesome-solid.js') }}"></script>
    <script defer src="{{ mix('js/fontawesome.js') }}"></script>
</head>
<body>
    <div id="app">
        <div class="container">
            <nav class="navbar is-transparent" style="background-color: transparent;">
                <div class="navbar-brand">
                    <a class="navbar-item" href="{{ url('/') }}">
                        <!--<img src="/logo.png" alt="Logo" width="200" height="40">-->
                        Server Tracker
                    </a>

                    <div class="navbar-burger burger" data-target="navMainMenu">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
                <div id="navMainMenu" class="navbar-menu">
                    <div class="navbar-end">
                        <div class="navbar-item">
                            <form action="{{ route('search') }}">
                                <search-box :data="{{ json_encode(request()->input('q')) }}"></search-box>
                            </form>
                        </div>
                        <a class="navbar-item" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </div>
                </div>
            </nav>
        </div>
        <!-- End Top Nav -->

        <section class="section">
            <div class="container">

                <div class="columns">
                    <div class="column is-2">

                        <!-- Main Menu -->
                        <aside class="menu">
                            <p class="menu-label">
                                Menu
                            </p>
                            <ul class="menu-list">
                                <li>
                                    <a class="{{ $menu == 'dashboard' ? ' is-active' : '' }}" href="{{ route('dashboard') }}">
                                        <span class="icon is-small">
                                            <i class="fa fa-tachometer"></i>
                                        </span>
                                        <span>
                                            Dashboard
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a class="{{ $menu == 'servers' ? ' is-active' : '' }}" href="{{ route('servers.index') }}">
                                        <span class="icon is-small">
                                            <i class="fa fa-server"></i>
                                        </span>
                                        <span>
                                            Servers
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a class="{{ $menu == 'accounts' ? ' is-active' : '' }}" href="{{ route('accounts.index') }}">
                                        <span class="icon is-small">
                                            <i class="fa fa-globe"></i>
                                        </span>
                                        <span>
                                            Accounts
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a class="{{ $menu == 'users' ? ' is-active' : '' }}" href="{{ route('users.index') }}">
                                        <span class="icon is-small">
                                            <i class="fa fa-users"></i>
                                        </span>
                                        <span>
                                            Users
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </aside>
                        <!-- End Main Menu -->

                    </div>
                    <div class="column">

                        <!-- Main Content  -->
                        @yield('content')

                    </div>
                </div>


            </div>
        </section>

    </div>

    <footer class="footer" style="background-color: inherit;">
        <div class="container">
            <div class="content has-text-centered">
                &copy; 2017 Grant Williams
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="{{ mix('js/manifest.js') }}"></script>
    <script src="{{ mix('js/vendor.js') }}"></script>
    <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>