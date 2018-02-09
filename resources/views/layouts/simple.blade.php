<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Server Tracker') }}</title>

    <!-- Styles -->
    <link rel="icon" href="data:;base64,iVBORwOKGO=">
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">

    <!-- Scripts -->
    <script defer src="{{ mix('js/fontawesome-solid.js') }}"></script>
    <script defer src="{{ mix('js/fontawesome.js') }}"></script>
</head>
<body>
    <div id="app">

        <section class="section">
            <div class="container">
                @yield('content')
            </div>
        </section>

    </div>

    <footer class="footer" style="background-color: inherit;">
        <div class="container">
            <div class="content has-text-centered">
                &copy; {{ date('Y') }} Grant Williams
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="{{ mix('js/manifest.js') }}"></script>
    <script src="{{ mix('js/vendor.js') }}"></script>
    <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>