<header x-data="{ open: false }" class="bg-sky-600 shadow">
  <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
    <div class="flex justify-between h-16">
      <div class="flex px-2 lg:px-0">
        <div class="flex-shrink-0 flex items-center">
          <a href="{{ route('dashboard') }}">
            <img class="h-14 w-14" src="/logo-cloud.svg" alt="{{ config('app.name') }}">
          </a>
        </div>

        <nav aria-label="Global" class="hidden lg:ml-10 lg:flex lg:items-center lg:space-x-4">
          <x-layouts.navigation-items/>
        </nav>
      </div>

      <div class="flex-1 px-2 items-center flex justify-center lg:ml-6 lg:justify-end">
        <div class="max-w-lg w-full lg:max-w-xs">
          <label for="search" class="sr-only">Search</label>
          <div x-data="SearchComponent()"
               @keydown.meta.k.window.prevent="focusBox()"
               class="relative text-gray-400 focus-within:text-gray-600">
            <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
              <x-heroicon-s-search class="h-5 w-5"/>
            </div>
            <input id="search"
                   x-ref="search"
                   class="block w-full bg-white py-2 pl-10 pr-3 border border-transparent rounded-md leading-5 text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-sky-600 focus:ring-white focus:border-white sm:text-sm"
                   placeholder="Search" type="search" name="search">
            <div class="absolute inset-y-0 right-0 flex py-1.5 pr-1.5">
              <kbd
                class="inline-flex items-center border border-gray-200 rounded px-2 text-sm font-sans font-medium text-gray-400">
                ⌘K
              </kbd>
            </div>
          </div>
        </div>
      </div>
      <div class="flex items-center lg:hidden">
        <!-- Mobile menu button -->
        <button @click="open = !open" type="button"
                class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500"
                aria-expanded="false">
          <span class="sr-only">Open main menu</span>
          <x-heroicon-o-menu class="block h-6 w-6"/>
        </button>
      </div>

      <!-- Mobile menu -->
      <div :class="{'block': open, 'hidden': !open }" class="lg:hidden">
        <div class="z-20 fixed inset-0 bg-black bg-opacity-25" aria-hidden="true"></div>

        <div x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="transform opacity-0 scale-95"
             x-transition:enter-end="transform opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="transform opacity-100 scale-100"
             x-transition:leave-end="transform opacity-0 scale-95"
             class="z-30 absolute top-0 right-0 max-w-none w-full p-2 transition transform origin-top"
             x-cloak>
          <div class="rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 bg-white divide-y divide-gray-200">
            <div class="pt-3 pb-2">
              <div class="flex items-center justify-between px-4">
                <div>
                  <img class="h-8 w-auto" src="https://tailwindui.com/img/logos/workflow-mark-blue-600.svg"
                       alt="Workflow">
                </div>
                <div class="-mr-2">
                  <button @click="open = !open" type="button"
                          class="bg-white rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
                    <span class="sr-only">Close menu</span>
                    <x-heroicon-o-x class="h-6 w-6"/>
                  </button>
                </div>
              </div>
              <div class="mt-3 px-2 space-y-1">
                <x-layouts.navigation-items is-mobile-menu/>
              </div>
            </div>
            <div class="pt-4 pb-2">
              <div class="flex items-center px-5">
                <x-heroicon-s-user-circle class="h-10 w-10 text-gray-800"/>
                <div class="ml-3">
                  <div class="text-base font-medium text-gray-800">{{ auth()->user()->name }}</div>
                </div>
              </div>
              <div class="mt-3 px-2 space-y-1">
                <a href="{{ route('users.index') }}"
                   class="block rounded-md px-3 py-2 text-base text-gray-900 font-medium hover:bg-gray-100 hover:text-gray-800">Manage
                  Users</a>
                <x-navigation.logout
                  class="w-full text-left block rounded-md px-3 py-2 text-base text-gray-900 font-medium hover:bg-gray-100 hover:text-gray-800">
                  Sign out
                </x-navigation.logout>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="hidden lg:ml-4 lg:flex lg:items-center">
        <!-- Profile dropdown -->
        <x-navigation.dropdown class="ml-3 flex-shrink-0">
          <x-slot name="trigger">
            <button type="button"
                    class="max-w-xs bg-sky-600 rounded-full flex items-center text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 lg:p-2 lg:rounded-md lg:hover:bg-sky-500"
                    id="user-menu-button" aria-expanded="false" aria-haspopup="true">
              <x-heroicon-s-user-circle class="h-5 w-5 text-white"/>
              <span class="hidden ml-2 text-white text-sm font-medium lg:block"><span
                  class="sr-only">Open user menu for </span>{{ auth()->user()->name }}</span>
              <x-heroicon-s-chevron-down class="hidden flex-shrink-0 ml-1 h-5 w-5 text-white lg:block"/>
            </button>
          </x-slot>

          <div
            class="origin-top-right z-50 absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
            role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
            <a href="{{ route('users.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
               role="menuitem" tabindex="-1" id="user-menu-item-0">Manager Users</a>
            <x-navigation.logout class="w-full block text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                 role="menuitem" tabindex="-1" id="user-menu-item-1">Sign out
            </x-navigation.logout>
          </div>
        </x-navigation.dropdown>
      </div>
    </div>
  </div>
</header>
