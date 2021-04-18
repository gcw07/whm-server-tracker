<nav x-data="{ open: false }" class="bg-blue-700">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-16">
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <img class="h-10 w-24" src="/img/server-tracker-logo.svg" alt="WHM Server Tracker" />
        </div>
        <div class="hidden md:block">
          <div class="ml-10 flex items-baseline space-x-4">
            <x-layouts.navigation-item :href="route('dashboard')">Dashboard</x-layouts.navigation-item>
            <x-layouts.navigation-item :href="route('servers.index')">Servers</x-layouts.navigation-item>
            <x-layouts.navigation-item :href="route('accounts.index')">Accounts</x-layouts.navigation-item>
            <x-layouts.navigation-item :href="route('users.index')">Users</x-layouts.navigation-item>
          </div>
        </div>
      </div>
      <div class="hidden md:block">
        <div class="ml-4 flex items-center md:ml-6">
          <button class="bg-blue-600 p-1 rounded-full text-blue-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-blue-600 focus:ring-white">
            <span class="sr-only">Search</span>
            <x-heroicon-o-search class="h-6 w-6"/>
          </button>

          <!-- Profile dropdown -->
          <x-dropdown class="ml-3 relative">
            <x-slot name="trigger">
              <button type="button" class="max-w-xs bg-blue-600 rounded-full flex items-center text-sm text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-blue-600 focus:ring-white" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                <span class="sr-only">Open user menu</span>
                <x-avatar search="gcw07" class="h-8 w-8 rounded-full" />
              </button>
            </x-slot>

            <div
              x-transition:enter="transition ease-out duration-100"
              x-transition:enter-start="transform opacity-0 scale-95"
              x-transition:enter-end="transform opacity-100 scale-100"
              x-transition:leave="transition ease-in duration-75"
              x-transition:leave-start="transform opacity-100 scale-100"
              x-transition:leave-end="transform opacity-0 scale-95"
              class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" x-cloak>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700">Your Profile</a>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700">Settings</a>
                <x-logout class="block px-4 py-2 text-sm text-gray-700">
                  Sign out
                </x-logout>
            </div>
          </x-dropdown>
        </div>
      </div>
      <div class="-mr-2 flex md:hidden">
        <!-- Mobile menu button -->
        <button @click="open = !open" type="button" class="bg-blue-600 inline-flex items-center justify-center p-2 rounded-md text-blue-200 hover:text-white hover:bg-blue-500 hover:bg-opacity-75 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-blue-600 focus:ring-white" aria-controls="mobile-menu" aria-expanded="false">
          <span class="sr-only">Open main menu</span>
          <x-heroicon-o-menu ::class="{'hidden': open, 'block': !open }" class="h-6 w-6"/>
          <x-heroicon-o-x ::class="{'block': open, 'hidden': !open }" class="h-6 w-6"/>
        </button>
      </div>
    </div>
  </div>

  <!-- Mobile menu, show/hide based on menu state. -->
  <div :class="{'block': open, 'hidden': !open }" class="md:hidden" id="mobile-menu">
    <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
      <x-layouts.navigation-item :href="route('dashboard')" :mobile="true">Dashboard</x-layouts.navigation-item>
      <x-layouts.navigation-item :href="route('servers.index')" :mobile="true" class="mt-1">Servers</x-layouts.navigation-item>
      <x-layouts.navigation-item :href="route('accounts.index')" :mobile="true" class="mt-1">Accounts</x-layouts.navigation-item>
      <x-layouts.navigation-item :href="route('users.index')" :mobile="true" class="mt-1">Users</x-layouts.navigation-item>
    </div>
    <div class="pt-4 pb-3 border-t border-indigo-700">
      <div class="flex items-center px-5">
        <div class="flex-shrink-0">
          <img class="h-10 w-10 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixqx=g44WKpb6Vn&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">
        </div>
        <div class="ml-3">
          <div class="text-base font-medium text-white">Tom Cook</div>
          <div class="text-sm font-medium text-indigo-300">tom@example.com</div>
        </div>
        <button class="ml-auto bg-indigo-600 flex-shrink-0 p-1 border-2 border-transparent rounded-full text-indigo-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-indigo-600 focus:ring-white">
          <span class="sr-only">View notifications</span>
          <!-- Heroicon name: outline/bell -->
          <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
          </svg>
        </button>
      </div>
      <div class="mt-3 px-2 space-y-1">
        <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-indigo-500 hover:bg-opacity-75">Your Profile</a>
        <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-indigo-500 hover:bg-opacity-75">Settings</a>
        <x-logout class="block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-indigo-500 hover:bg-opacity-75">
          Sign out
        </x-logout>
      </div>
    </div>
  </div>
</nav>
