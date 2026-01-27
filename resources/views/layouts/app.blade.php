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
    <body class="min-h-screen bg-gray-100 dark:bg-zinc-800 antialiased">
        <flux:header container class="bg-cyan-600 dark:bg-zinc-900 border-b border-zinc-400 dark:border-zinc-700 h-16">
          <flux:sidebar.toggle class="lg:hidden text-white!" icon="bars-2" inset="left" />

          <flux:brand href="{{ route('dashboard') }}" class="max-lg:hidden dark:hidden me-8">
            <x-slot name="logo" class="px-0 h-14!">
              <img src="/images/logo.svg" alt="{{ config('app.name') }}" class="h-14" />
            </x-slot>
          </flux:brand>
          <flux:brand href="{{ route('dashboard') }}" class="max-lg:hidden! hidden dark:flex">
            <x-slot name="logo" class="px-0 h-14!">
              <img src="/images/logo.svg" alt="{{ config('app.name') }}" class="h-14" />
            </x-slot>
          </flux:brand>

          <flux:navbar class="-mb-px gap-6 max-lg:hidden">
            <flux:navbar.item icon="server" iconVariant="solid" :href="route('servers.index')">Servers</flux:navbar.item>
            <flux:navbar.item icon="globe-alt" iconVariant="solid" :href="route('accounts.index')">Accounts</flux:navbar.item>
            <flux:navbar.item icon="sparkles" iconVariant="solid" :href="route('monitors.index')">Monitors</flux:navbar.item>
          </flux:navbar>

          <flux:spacer />

          <flux:navbar class="me-8">
            <flux:input as="button" placeholder="Search..." icon="magnifying-glass" kbd="⌘K" class="hover:bg-gray-100" />
          </flux:navbar>

          <flux:dropdown position="bottom" align="end" class="max-lg:hidden">
            <flux:profile name="{{ auth()->user()->name }}" class="hover:bg-cyan-500!" />

            <flux:menu>
              <flux:menu.item href="{{ route('users.index') }}" icon="users">Manage Users</flux:menu.item>

              <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <flux:menu.item
                  as="button"
                  type="submit"
                  icon="arrow-right-start-on-rectangle"
                  class="w-full cursor-pointer"
                  data-test="logout-button"
                >
                  Sign out
                </flux:menu.item>
              </form>
            </flux:menu>
          </flux:dropdown>
        </flux:header>

        <flux:sidebar sticky collapsible="mobile" class="lg:hidden bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
          <flux:sidebar.header>
            <flux:sidebar.brand href="{{ route('dashboard') }}">
              <x-slot name="logo" class="px-0 h-10!">
                <img src="/images/logo-solid.svg" alt="{{ config('app.name') }}" class="h-10 dark:hidden" />
                <img src="/images/logo.svg" alt="{{ config('app.name') }}" class="h-10 hidden dark:block" />
              </x-slot>
            </flux:sidebar.brand>

            <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
          </flux:sidebar.header>

          <flux:sidebar.nav>
            <flux:sidebar.item icon="home" href="{{ route('dashboard') }}">Dashboard</flux:sidebar.item>
            <flux:sidebar.item icon="server" href="{{ route('servers.index') }}">Servers</flux:sidebar.item>
            <flux:sidebar.item icon="globe-alt" href="{{ route('accounts.index') }}">Accounts</flux:sidebar.item>
            <flux:sidebar.item icon="sparkles" href="{{ route('monitors.index') }}">Monitors</flux:sidebar.item>
            <flux:sidebar.item icon="users" href="{{ route('users.index') }}">Manage Users</flux:sidebar.item>
          </flux:sidebar.nav>

          <flux:sidebar.spacer />

          <flux:sidebar.nav>
            <form method="POST" action="{{ route('logout') }}" class="w-full">
              @csrf
              <flux:sidebar.item
                as="button"
                type="submit"
                icon="arrow-right-start-on-rectangle"
                class="w-full cursor-pointer"
                data-test="logout-button"
              >
                Sign out
              </flux:sidebar.item>
            </form>
          </flux:sidebar.nav>
        </flux:sidebar>

        <flux:main container>
          {{ $slot }}
        </flux:main>

        @livewireScriptConfig
        @fluxScripts
    </body>
</html>
