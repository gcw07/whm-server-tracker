<x-table>
  <x-slot name="head">
    <x-table.heading sortable wire:click="sortBy('name')" :direction="$sortField === 'name' ? $sortDirection : null" class="rounded-tl-lg">Name</x-table.heading>
    <x-table.heading sortable wire:click="sortBy('email')" :direction="$sortField === 'email' ? $sortDirection : null">Email</x-table.heading>
    <x-table.heading>Last Login</x-table.heading>
    <x-table.heading>Last IP Address</x-table.heading>
    <x-table.heading class="rounded-tr-lg" />
  </x-slot>
  <x-slot name="body">
    @forelse ($users as $user)
      <x-table.row wire:loading.class.delay="opacity-50" wire:key="row-{{ $user->id }}">
        @if ($loop->last)
          <x-table.cell class="text-gray-900 font-medium rounded-bl-lg">
            {{ $user->name }}
          </x-table.cell>
        @else
          <x-table.cell class="text-gray-900 font-medium">
            {{ $user->name }}
          </x-table.cell>
        @endif

          <x-table.cell>
            {{ $user->email }}
          </x-table.cell>

          <x-table.cell>
            <span>{{ $user->lastLogin->created_at->diffForHumans() }}</span>
          </x-table.cell>

          <x-table.cell>
            {{ $user->lastLogin->ip_address }}
          </x-table.cell>

        @if ($loop->last)
          <x-table.cell class="flex justify-end rounded-br-lg">
            <!-- Row action dropdown -->
            <x-dropdown class="relative">
              <x-slot name="trigger">
                <button type="button" class="max-w-xs rounded flex items-center text-sm text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-600 focus:ring-white" aria-expanded="false" aria-haspopup="true">
                  <span class="sr-only">Open action menu</span>
                  <x-heroicon-o-dots-horizontal class="h-6 w-6 text-gray-500"/>
                </button>
              </x-slot>

              <div
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" x-cloak>
                <div class="px-1 py-1">
                  <a href="#" class="block px-2 py-2 text-sm text-gray-700 rounded hover:bg-gray-200">Your Profile</a>
                  <a href="#" class="block px-2 py-2 text-sm text-gray-700 rounded hover:bg-gray-200">Settings</a>
                </div>
              </div>
            </x-dropdown>
          </x-table.cell>
        @else
            <x-table.cell class="flex justify-end">
              <!-- Row action dropdown -->
              <x-dropdown class="relative">
                <x-slot name="trigger">
                  <button type="button" class="max-w-xs rounded flex items-center text-sm text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-600 focus:ring-white" aria-expanded="false" aria-haspopup="true">
                    <span class="sr-only">Open action menu</span>
                    <x-heroicon-o-dots-horizontal class="h-6 w-6 text-gray-500"/>
                  </button>
                </x-slot>

                <div
                  x-transition:enter="transition ease-out duration-100"
                  x-transition:enter-start="transform opacity-0 scale-95"
                  x-transition:enter-end="transform opacity-100 scale-100"
                  x-transition:leave="transition ease-in duration-75"
                  x-transition:leave-start="transform opacity-100 scale-100"
                  x-transition:leave-end="transform opacity-0 scale-95"
                  class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" x-cloak>
                  <div class="px-1 py-1">
                    <a href="#" class="block px-2 py-2 text-sm text-gray-700 rounded hover:bg-gray-200">Your Profile</a>
                    <a href="#" class="block px-2 py-2 text-sm text-gray-700 rounded hover:bg-gray-200">Settings</a>
                  </div>
                </div>
              </x-dropdown>
            </x-table.cell>
        @endif
      </x-table.row>
    @empty
      <x-table.row>
        <x-table.cell colspan="5">
          <div class="flex justify-center items-center space-x-2">
            <x-heroicon-o-user class="h-8 w-8 text-gray-400"/>
            <span class="font-medium py-8 text-gray-400 text-xl">No users found...</span>
          </div>
        </x-table.cell>
      </x-table.row>
    @endforelse
  </x-slot>
</x-table>

<div>
  {{ $users->links() }}
</div>
