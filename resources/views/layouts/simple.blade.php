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
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
      <div class="bg-background flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
        <div class="flex w-full max-w-sm flex-col gap-2">
{{--          <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-2 font-medium" wire:navigate>--}}
{{--                          <span class="flex h-9 w-9 mb-1 items-center justify-center rounded-md">--}}
{{--                              <x-app-logo-icon class="size-9 fill-current text-black dark:text-white" />--}}
{{--                          </span>--}}
{{--            <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>--}}
{{--          </a>--}}
          <div class="flex flex-col gap-6">
            {{ $slot }}
          </div>
        </div>
      </div>

      @livewireScriptConfig
      @fluxScripts
    </body>
</html>
