<header x-data="{ open: false }" class="bg-sky-600 shadow">
  <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
    <div class="flex justify-between h-16">
      <div class="flex px-2 lg:px-0">
        <div class="flex-shrink-0 flex items-center">
          <a href="#">
            <img class="h-14 w-14" src="/logo-cloud.svg" alt="Workflow">
          </a>
        </div>

        <nav aria-label="Global" class="hidden lg:ml-10 lg:flex lg:items-center lg:space-x-4">
          <x-layouts.navigation-items />
        </nav>
      </div>

      <div class="flex-1 px-2 items-center flex justify-center lg:ml-6 lg:justify-end">
        <div class="max-w-lg w-full lg:max-w-xs">
          <label for="search" class="sr-only">Search</label>
          <div class="relative text-gray-400 focus-within:text-gray-600">
            <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
              <x-heroicon-s-search class="h-5 w-5" />
            </div>
            <input id="search" class="block w-full bg-white py-2 pl-10 pr-3 border border-transparent rounded-md leading-5 text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-sky-600 focus:ring-white focus:border-white sm:text-sm" placeholder="Search" type="search" name="search">
            <div class="absolute inset-y-0 right-0 flex py-1.5 pr-1.5">
              <kbd class="inline-flex items-center border border-gray-200 rounded px-2 text-sm font-sans font-medium text-gray-400">
                ⌘K
              </kbd>
            </div>
          </div>
        </div>
      </div>
      <div class="flex items-center lg:hidden">
        <!-- Mobile menu button -->
        <button @click="open = !open" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500" aria-expanded="false">
          <span class="sr-only">Open main menu</span>
          <x-heroicon-o-menu class="block h-6 w-6" />
        </button>
      </div>

      <!-- Mobile menu, show/hide based on mobile menu state. -->
      <div :class="{'block': open, 'hidden': !open }" class="lg:hidden">
        <!--
          Mobile menu overlay, show/hide based on mobile menu state.

          Entering: "duration-150 ease-out"
            From: "opacity-0"
            To: "opacity-100"
          Leaving: "duration-150 ease-in"
            From: "opacity-100"
            To: "opacity-0"
        -->
        <div class="z-20 fixed inset-0 bg-black bg-opacity-25" aria-hidden="true"></div>

        <!--
          Mobile menu, show/hide based on mobile menu state.

          Entering: "duration-150 ease-out"
            From: "opacity-0 scale-95"
            To: "opacity-100 scale-100"
          Leaving: "duration-150 ease-in"
            From: "opacity-100 scale-100"
            To: "opacity-0 scale-95"
        -->
        <div class="z-30 absolute top-0 right-0 max-w-none w-full p-2 transition transform origin-top">
          <div class="rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 bg-white divide-y divide-gray-200">
            <div class="pt-3 pb-2">
              <div class="flex items-center justify-between px-4">
                <div>
                  <img class="h-8 w-auto" src="https://tailwindui.com/img/logos/workflow-mark-blue-600.svg" alt="Workflow">
                </div>
                <div class="-mr-2">
                  <button @click="open = !open" type="button" class="bg-white rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
                    <span class="sr-only">Close menu</span>
                    <x-heroicon-o-x class="h-6 w-6" />
                  </button>
                </div>
              </div>
              <div class="mt-3 px-2 space-y-1">
                <a href="#" class="block rounded-md px-3 py-2 text-base text-gray-900 font-medium hover:bg-gray-100 hover:text-gray-800">Dashboard</a>

                <a href="#" class="block rounded-md px-3 py-2 text-base text-gray-900 font-medium hover:bg-gray-100 hover:text-gray-800">Jobs</a>

                <a href="#" class="block rounded-md px-3 py-2 text-base text-gray-900 font-medium hover:bg-gray-100 hover:text-gray-800">Applicants</a>

                <a href="#" class="block rounded-md px-3 py-2 text-base text-gray-900 font-medium hover:bg-gray-100 hover:text-gray-800">Company</a>
              </div>
            </div>
            <div class="pt-4 pb-2">
              <div class="flex items-center px-5">
                <div class="flex-shrink-0">
                  <img class="h-10 w-10 rounded-full" src="https://images.unsplash.com/photo-1517365830460-955ce3ccd263?ixlib=rb-=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=8&w=256&h=256&q=80" alt="">
                </div>
                <div class="ml-3">
                  <div class="text-base font-medium text-gray-800">Whitney Francis</div>
                  <div class="text-sm font-medium text-gray-500">whitney@example.com</div>
                </div>
              </div>
              <div class="mt-3 px-2 space-y-1">
                <a href="#" class="block rounded-md px-3 py-2 text-base text-gray-900 font-medium hover:bg-gray-100 hover:text-gray-800">Your Profile</a>

                <a href="#" class="block rounded-md px-3 py-2 text-base text-gray-900 font-medium hover:bg-gray-100 hover:text-gray-800">Settings</a>

                <a href="#" class="block rounded-md px-3 py-2 text-base text-gray-900 font-medium hover:bg-gray-100 hover:text-gray-800">Sign out</a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="hidden lg:ml-4 lg:flex lg:items-center">
        <!-- Profile dropdown -->
        <x-navigation.dropdown class="ml-3 flex-shrink-0 x-cloak">
          <x-slot name="trigger">
            <button type="button" class="max-w-xs bg-sky-600 rounded-full flex items-center text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 lg:p-2 lg:rounded-md lg:hover:bg-sky-500" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
              <x-heroicon-s-user-circle class="h-5 w-5 text-white" />
              <span class="hidden ml-2 text-white text-sm font-medium lg:block"><span class="sr-only">Open user menu for </span>Emilia Birch</span>
              <x-heroicon-s-chevron-down class="hidden flex-shrink-0 ml-1 h-5 w-5 text-white lg:block" />
            </button>
          </x-slot>

          <div class="x-cloak origin-top-right z-20 absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1" id="user-menu-item-0">Manager Users</a>
            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1" id="user-menu-item-2">Sign out</a>
          </div>
        </x-navigation.dropdown>
      </div>
    </div>
  </div>
</header>




{{--<nav x-data="{ open: false }" class="bg-blue-700">--}}
{{--  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">--}}
{{--    <div class="flex items-center justify-between h-16">--}}
{{--      <div class="flex items-center">--}}
{{--        <div class="flex-shrink-0">--}}
{{--          <img class="h-10 w-24" src="/img/server-tracker-logo.svg" alt="WHM Server Tracker" />--}}
{{--        </div>--}}
{{--        <div class="hidden md:block">--}}
{{--          <div class="ml-10 flex items-baseline space-x-4">--}}
{{--            <x-layouts.navigation-item :href="route('dashboard')">Dashboard</x-layouts.navigation-item>--}}
{{--            <x-layouts.navigation-item :href="route('servers.index')">Servers</x-layouts.navigation-item>--}}
{{--            <x-layouts.navigation-item :href="route('accounts.index')">Accounts</x-layouts.navigation-item>--}}
{{--            <x-layouts.navigation-item :href="route('users.index')">Users</x-layouts.navigation-item>--}}
{{--          </div>--}}
{{--        </div>--}}
{{--      </div>--}}
{{--      <div class="hidden md:block">--}}
{{--        <div class="ml-4 flex items-center md:ml-6">--}}
{{--          <button class="bg-blue-600 p-1 rounded-full text-blue-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-blue-600 focus:ring-white">--}}
{{--            <span class="sr-only">Search</span>--}}
{{--            <x-heroicon-o-search class="h-6 w-6"/>--}}
{{--          </button>--}}

{{--          <!-- Profile dropdown -->--}}
{{--          <x-dropdown class="ml-3 relative">--}}
{{--            <x-slot name="trigger">--}}
{{--              <button type="button" class="max-w-xs bg-blue-600 rounded-full flex items-center text-sm text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-blue-600 focus:ring-white" id="user-menu-button" aria-expanded="false" aria-haspopup="true">--}}
{{--                <span class="sr-only">Open user menu</span>--}}
{{--                <x-avatar search="test@email.com" class="h-8 w-8 rounded-full" />--}}
{{--              </button>--}}
{{--            </x-slot>--}}

{{--            <div--}}
{{--              x-transition:enter="transition ease-out duration-100"--}}
{{--              x-transition:enter-start="transform opacity-0 scale-95"--}}
{{--              x-transition:enter-end="transform opacity-100 scale-100"--}}
{{--              x-transition:leave="transition ease-in duration-75"--}}
{{--              x-transition:leave-start="transform opacity-100 scale-100"--}}
{{--              x-transition:leave-end="transform opacity-0 scale-95"--}}
{{--              class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" x-cloak>--}}
{{--              <div class="px-1 py-1">--}}
{{--                <a href="#" class="flex items-center w-full px-2 py-2 text-sm text-gray-700 rounded hover:bg-gray-200">--}}
{{--                  <x-heroicon-o-user class="h-4 w-4 mr-2"/>--}}
{{--                  Your Profile--}}
{{--                </a>--}}
{{--                <x-logout class="flex items-center w-full px-2 py-2 text-sm text-gray-700 rounded hover:bg-gray-200">--}}
{{--                  <x-heroicon-o-logout class="h-4 w-4 mr-2"/>--}}
{{--                  Sign Out--}}
{{--                </x-logout>--}}
{{--              </div>--}}
{{--            </div>--}}
{{--          </x-dropdown>--}}
{{--        </div>--}}
{{--      </div>--}}
{{--      <div class="-mr-2 flex md:hidden">--}}
{{--        <!-- Mobile menu button -->--}}
{{--        <button @click="open = !open" type="button" class="bg-blue-600 inline-flex items-center justify-center p-2 rounded-md text-blue-200 hover:text-white hover:bg-blue-500 hover:bg-opacity-75 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-blue-600 focus:ring-white" aria-controls="mobile-menu" aria-expanded="false">--}}
{{--          <span class="sr-only">Open main menu</span>--}}
{{--          <x-heroicon-o-menu ::class="{'hidden': open, 'block': !open }" class="h-6 w-6"/>--}}
{{--          <x-heroicon-o-x ::class="{'block': open, 'hidden': !open }" class="h-6 w-6"/>--}}
{{--        </button>--}}
{{--      </div>--}}
{{--    </div>--}}
{{--  </div>--}}

{{--  <!-- Mobile menu, show/hide based on menu state. -->--}}
{{--  <div :class="{'block': open, 'hidden': !open }" class="md:hidden" id="mobile-menu">--}}
{{--    <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">--}}
{{--      <x-layouts.navigation-item :href="route('dashboard')" :mobile="true">Dashboard</x-layouts.navigation-item>--}}
{{--      <x-layouts.navigation-item :href="route('servers.index')" :mobile="true" class="mt-1">Servers</x-layouts.navigation-item>--}}
{{--      <x-layouts.navigation-item :href="route('accounts.index')" :mobile="true" class="mt-1">Accounts</x-layouts.navigation-item>--}}
{{--      <x-layouts.navigation-item :href="route('users.index')" :mobile="true" class="mt-1">Users</x-layouts.navigation-item>--}}
{{--    </div>--}}
{{--    <div class="pt-4 pb-3 border-t border-indigo-700">--}}
{{--      <div class="flex items-center px-5">--}}
{{--        <div class="flex-shrink-0">--}}
{{--          <img class="h-10 w-10 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixqx=g44WKpb6Vn&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">--}}
{{--        </div>--}}
{{--        <div class="ml-3">--}}
{{--          <div class="text-base font-medium text-white">Tom Cook</div>--}}
{{--          <div class="text-sm font-medium text-indigo-300">tom@example.com</div>--}}
{{--        </div>--}}
{{--        <button class="ml-auto bg-indigo-600 flex-shrink-0 p-1 border-2 border-transparent rounded-full text-indigo-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-indigo-600 focus:ring-white">--}}
{{--          <span class="sr-only">View notifications</span>--}}
{{--          <!-- Heroicon name: outline/bell -->--}}
{{--          <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">--}}
{{--            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />--}}
{{--          </svg>--}}
{{--        </button>--}}
{{--      </div>--}}
{{--      <div class="mt-3 px-2 space-y-1">--}}
{{--        <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-indigo-500 hover:bg-opacity-75">Your Profile</a>--}}
{{--        <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-indigo-500 hover:bg-opacity-75">Settings</a>--}}
{{--        <x-logout class="block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-indigo-500 hover:bg-opacity-75">--}}
{{--          Sign out--}}
{{--        </x-logout>--}}
{{--      </div>--}}
{{--    </div>--}}
{{--  </div>--}}
{{--</nav>--}}
