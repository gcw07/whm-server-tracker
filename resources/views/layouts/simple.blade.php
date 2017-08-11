<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Server Tracker') }}</title>

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">

        <section class="section">
            <div class="container">
                @yield('content')
            </div>
        </section>

    </div>

    <footer class="footer">
        <div class="container">
            <div class="content has-text-centered">
                &copy; 2017 Grant Williams
            </div>
        </div>
    </footer>

</body>
</html>