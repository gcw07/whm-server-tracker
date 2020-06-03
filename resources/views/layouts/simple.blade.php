<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="mobile-web-app-capable" content="yes">
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @isset($title)
    <title>{{ $title }} - WHM Server Tracker</title>
  @else
    <title>WHM Server Tracker</title>
  @endisset

  <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
  <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body>
  {{ $slot }}
</body>
</html>
