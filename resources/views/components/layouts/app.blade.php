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
  <link rel="stylesheet" href="{{ mix('css/app.css') }}">
  @livewireStyles

  <!-- Scripts -->
  <script src="{{ mix('js/app.js') }}" defer></script>
</head>
<body class="h-full font-sans antialiased">

<div class="min-h-full">
  <x-layouts.navigation />

  <main class="py-10">
    <div class="max-w-7xl mx-auto pb-12 px-4 sm:px-6 lg:px-8">

      {{ $slot }}

    </div>
  </main>
</div>

@livewire('livewire-ui-modal')
@livewireScripts
</body>
</html>
