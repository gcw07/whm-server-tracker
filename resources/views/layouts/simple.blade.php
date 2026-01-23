<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta name="csrf-token" content="{{ csrf_token() }}">

      <title>{{ $title ?? config('app.name') }}</title>

      <!-- Fonts -->
      <link rel="preconnect" href="https://fonts.bunny.net">
      <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />

      @vite(['resources/css/app.css', 'resources/js/app.js'])

      @livewireStyles
      @fluxAppearance
    </head>
    <body class="min-h-screen bg-gray-50 antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
      <div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">
          {{ $slot }}
      </div>
      
      @livewireScriptConfig
      @fluxScripts
    </body>
</html>
