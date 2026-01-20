<!DOCTYPE html>
<html class="h-full bg-gray-100" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ isset($title) ? $title . ' | ' . config('app.name') : '' }}</title>

  <!-- Fonts -->
  <link rel="stylesheet" href="https://rsms.me/inter/inter.css">

  <!-- Styles -->
  @vite('resources/css/app.css')
  @livewireStyles

  <!-- Scripts -->
  @toastScripts
  @vite('resources/js/app.js')
</head>
<body class="h-full font-sans antialiased">

<div class="relative bg-gray-200">
  <div class="absolute inset-0 flex flex-col" aria-hidden="true">
    <div class="flex-1 bg-gray-100"></div>
    <div class="flex-1 bg-gray-200"></div>
  </div>
  <div class="relative max-w-xl mx-auto">
    <div class="flex flex-col min-h-screen sm:flex-row sm:items-center sm:p-8">
      <div class="flex flex-col grow bg-white sm:shadow-2xl sm:rounded-lg sm:overflow-hidden">
        {{ $slot }}
      </div>
    </div>
  </div>
</div>

@livewireScriptConfig
</body>
</html>
