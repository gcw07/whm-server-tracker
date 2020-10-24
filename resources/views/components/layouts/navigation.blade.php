<nav x-data="{ open: false }" class="bg-blue-800">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-16">
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <img class="h-10 w-24" src="/img/server-tracker-logo.svg" alt="Logo" />
        </div>
        <div class="hidden md:block">
          <div class="ml-10 flex items-baseline">
            <x-layouts.navigation-item :href="route('dashboard')">Dashboard</x-layouts.navigation-item>
            <x-layouts.navigation-item :href="route('servers.index')" class="ml-4">Servers</x-layouts.navigation-item>
            <x-layouts.navigation-item :href="route('accounts.index')" class="ml-4">Accounts</x-layouts.navigation-item>
            <x-layouts.navigation-item :href="route('users.index')" class="ml-4">Users</x-layouts.navigation-item>
          </div>
        </div>
      </div>
      <div class="hidden md:block">
        <div class="ml-4 flex items-center md:ml-6">
          <button class="p-1 border-2 border-transparent text-indigo-300 rounded-full hover:text-white focus:outline-none focus:text-white focus:bg-indigo-600" aria-label="Notifications">
            <x-heroicon-o-search class="h-6 w-6"/>
          </button>

          <!-- Profile dropdown -->
          <x-dropdown class="ml-3 relative">
            <x-slot name="trigger">
              <button class="max-w-xs flex items-center text-sm rounded-full text-white focus:outline-none focus:shadow-solid" id="user-menu" aria-label="User menu" aria-haspopup="true">
                <x-avatar search="gcw07" provider="twitter" class="h-8 w-8 rounded-full" />
              </button>
            </x-slot>

            <div
                 x-transition:enter="transition ease-out duration-100 transform"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75 transform"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg">
              <div class="py-1 rounded-md bg-white shadow-xs">
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Your Profile</a>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                <x-logout class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                  Sign out
                </x-logout>
              </div>
            </div>
          </x-dropdown>
        </div>
      </div>
      <div class="-mr-2 flex md:hidden">
        <!-- Mobile menu button -->
        <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-blue-300 hover:text-white hover:bg-blue-600 focus:outline-none focus:bg-blue-600 focus:text-white">
          <svg :class="{'hidden': open, 'block': !open }" class="block h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
          <svg :class="{'block': open, 'hidden': !open }" class="hidden h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>
  </div>

  <!--
    Mobile menu, toggle classes based on menu state.
  -->
  <div :class="{'block': open, 'hidden': !open }" class="hidden md:hidden">
    <div class="px-2 pt-2 pb-3 sm:px-3">
      <x-layouts.navigation-item :href="route('dashboard')" mobile="true">Dashboard</x-layouts.navigation-item>
      <x-layouts.navigation-item :href="route('servers.index')" mobile="true" class="mt-1">Servers</x-layouts.navigation-item>
      <x-layouts.navigation-item :href="route('accounts.index')" mobile="true" class="mt-1">Accounts</x-layouts.navigation-item>
      <x-layouts.navigation-item :href="route('users.index')" mobile="true" class="mt-1">Users</x-layouts.navigation-item>
    </div>
    <div class="pt-4 pb-3 border-t border-gray-700">
      <div class="flex items-center px-5">
        <div class="flex-shrink-0">
          <img class="h-10 w-10 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="" />
        </div>
        <div class="ml-3">
          <div class="text-base font-medium leading-none text-white">Tom Cook</div>
          <div class="mt-1 text-sm font-medium leading-none text-indigo-300">tom@example.com</div>
        </div>
      </div>
      <div class="mt-3 px-2" role="menu" aria-orientation="vertical" aria-labelledby="user-menu">
        <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-indigo-300 hover:text-white hover:bg-indigo-600 focus:outline-none focus:text-white focus:bg-indigo-600" role="menuitem">Your Profile</a>
        <a href="#" class="mt-1 block px-3 py-2 rounded-md text-base font-medium text-indigo-300 hover:text-white hover:bg-indigo-600 focus:outline-none focus:text-white focus:bg-indigo-600" role="menuitem">Settings</a>
        <x-logout class="mt-1 block px-3 py-2 rounded-md text-base font-medium text-indigo-300 hover:text-white hover:bg-indigo-600 focus:outline-none focus:text-white focus:bg-indigo-600">
          Sign out
        </x-logout>
      </div>
    </div>
  </div>
</nav>
