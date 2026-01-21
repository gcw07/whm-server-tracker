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
        <flux:header container class="bg-cyan-600 dark:bg-zinc-900 border-b border-zinc-400 dark:border-zinc-700">
          <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

          <flux:brand href="#" logo="https://fluxui.dev/img/demo/logo.png" name="Acme Inc." class="max-lg:hidden dark:hidden" />
          <flux:brand href="#" logo="https://fluxui.dev/img/demo/dark-mode-logo.png" name="Acme Inc." class="max-lg:hidden! hidden dark:flex" />

          <flux:navbar class="-mb-px gap-6 max-lg:hidden">
            <flux:navbar.item icon="server" iconVariant="solid" href="#">Servers</flux:navbar.item>
            <flux:navbar.item icon="globe-alt" iconVariant="solid" href="#">Accounts</flux:navbar.item>
            <flux:navbar.item icon="sparkles" iconVariant="solid" href="#">Monitors</flux:navbar.item>
          </flux:navbar>

          <flux:spacer />

          <flux:navbar class="me-4">
            <flux:navbar.item icon="magnifying-glass" href="#" label="Search" />
            <flux:navbar.item class="max-lg:hidden" icon="cog-6-tooth" href="#" label="Settings" />
          </flux:navbar>

          <flux:dropdown position="top" align="start">
            <flux:profile avatar="https://fluxui.dev/img/demo/user.png" />

            <flux:menu>
              <flux:menu.radio.group>
                <flux:menu.radio checked>Olivia Martin</flux:menu.radio>
                <flux:menu.radio>Truly Delta</flux:menu.radio>
              </flux:menu.radio.group>

              <flux:menu.separator />

              <flux:menu.item icon="arrow-right-start-on-rectangle">Logout</flux:menu.item>
            </flux:menu>
          </flux:dropdown>
        </flux:header>

        <flux:sidebar sticky collapsible="mobile" class="lg:hidden bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
          <flux:sidebar.header>
            <flux:sidebar.brand
              href="#"
              logo="https://fluxui.dev/img/demo/logo.png"
              logo:dark="https://fluxui.dev/img/demo/dark-mode-logo.png"
              name="Acme Inc."
            />

            <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
          </flux:sidebar.header>

          <flux:sidebar.nav>
            <flux:sidebar.item icon="home" href="#" current>Home</flux:sidebar.item>
            <flux:sidebar.item icon="inbox" badge="12" href="#">Inbox</flux:sidebar.item>
            <flux:sidebar.item icon="document-text" href="#">Documents</flux:sidebar.item>
            <flux:sidebar.item icon="calendar" href="#">Calendar</flux:sidebar.item>

            <flux:sidebar.group expandable heading="Favorites" class="grid">
              <flux:sidebar.item href="#">Marketing site</flux:sidebar.item>
              <flux:sidebar.item href="#">Android app</flux:sidebar.item>
              <flux:sidebar.item href="#">Brand guidelines</flux:sidebar.item>
            </flux:sidebar.group>
          </flux:sidebar.nav>

          <flux:sidebar.spacer />

          <flux:sidebar.nav>
            <flux:sidebar.item icon="cog-6-tooth" href="#">Settings</flux:sidebar.item>
            <flux:sidebar.item icon="information-circle" href="#">Help</flux:sidebar.item>
          </flux:sidebar.nav>
        </flux:sidebar>

        <flux:main container>
          {{ $slot }}
        </flux:main>

        @livewireScriptConfig
        @fluxScripts
    </body>
</html>
