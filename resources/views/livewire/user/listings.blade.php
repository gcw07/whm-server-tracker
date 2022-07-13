<div>
  <!-- Page Header -->
  <div class="pb-5 border-b border-gray-200 sm:flex sm:items-center sm:justify-between">
    <h3 class="text-2xl leading-6 font-medium text-gray-900">
      Users
    </h3>
    <div class="mt-3 sm:mt-0">
      <a href="{{ route('users.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
        <x-heroicon-s-plus class="-ml-0.5 mr-2 h-4 w-4"/>
        Create User
      </a>
    </div>
  </div>
  <!-- / End Page Header -->

  <div class="mt-6">
    <!-- Begin content -->

    <!-- User list (smallest breakpoint only) -->
    <div class="shadow sm:hidden">
      <ul role="list" class="mt-2 divide-y divide-gray-200 overflow-hidden shadow sm:hidden">
        @forelse($users as $user)
          <li>
            <a href="#"
              @class([
               'block px-4 py-4 hover:bg-gray-50',
               'bg-gray-50' => $loop->even,
               'bg-white' => $loop->odd
              ])>
              <span class="flex items-center space-x-4">
                <span class="flex-1 flex space-x-2 truncate">
                  <span class="flex flex-col text-gray-500 text-sm truncate">
                    <span class="truncate">{{ $user->name }}</span>
                    <span>{{ $user->email }}</span>
                  </span>
                </span>
                <x-heroicon-s-chevron-right class="flex-shrink-0 h-5 w-5 text-gray-400"/>
              </span>
            </a>
          </li>
        @empty
          <li>
            <span class="block px-4 py-4 bg-white hover:bg-gray-50">
              No entries found.
            </span>
          </li>
        @endforelse
      </ul>

      <!-- Pagination -->
      {{ $users->links('livewire.pagination.index') }}
    </div>

    <!-- User table (small breakpoint and up) -->
    <div class="hidden sm:block">
      <div class="mx-auto">
        <div class="flex flex-col mt-2">
          <div class="align-middle min-w-full overflow-x-auto shadow overflow-hidden sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
              <thead>
                <tr>
                  <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Name
                  </th>
                  <th scope="col" class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Email
                  </th>
                  <th scope="col" class="hidden px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider lg:table-cell">
                    Last Login
                  </th>
                  <th scope="col" class="hidden px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider lg:table-cell">
                    Login IP
                  </th>
                  <th scope="col" class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <span class="sr-only">Manage</span>
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                  <tr @class([
                        'bg-gray-50' => $loop->even,
                        'bg-white' => $loop->odd
                    ])>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      <div class="flex">
                        <a href="#" class="group inline-flex space-x-2 truncate text-sm">
                          <p class="text-gray-500 truncate group-hover:text-gray-900">
                            {{ $user->name }}
                          </p>
                        </a>
                      </div>
                    </td>
                    <td class="px-6 py-4 text-center whitespace-nowrap text-sm text-gray-500">
                      <span class="text-gray-900 font-medium">{{ $user->email }}</span>
                    </td>
                    <td class="hidden px-6 py-4 text-center whitespace-nowrap text-sm text-gray-500 lg:table-cell">
                      <span class="text-gray-900 font-medium">{{ $user->lastLogin?->created_at->format('F j, Y, g:i a') }}</span>
                    </td>
                    <td class="hidden px-6 py-4 text-center whitespace-nowrap text-sm text-gray-500 lg:table-cell">
                      <span class="text-gray-900 font-medium">{{ $user->lastLogin?->ip_address }}</span>
                    </td>
                    <td class="px-6 py-4 text-right whitespace-nowrap text-sm text-gray-500">
                      <!-- User menu dropdown -->
                      <x-navigation.dropdown class="ml-2">
                        <x-slot name="trigger">
                          <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500" aria-expanded="false" aria-haspopup="true">
                            <x-heroicon-s-dots-vertical class="-ml-0.5 h-4 w-4" />
                          </button>
                        </x-slot>

                        <div
                          class="origin-top-right z-50 absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                          role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                          <a href="{{ route('users.edit', $user) }}" class="flex items-center group px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                             role="menuitem" tabindex="-1" id="details-menu-item-0">
                            <x-heroicon-s-pencil-alt class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" />
                            Edit
                          </a>
                          <button wire:click='$emit("openModal", "user.delete", {{ json_encode(["user" => $user->id]) }})' class="w-full flex items-center group px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                  role="menuitem" tabindex="-1" id="details-menu-item-1">
                            <x-heroicon-s-trash class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" />
                            Delete
                          </button>
                        </div>
                      </x-navigation.dropdown>
                    </td>
                  </tr>
                @empty
                  <tr class="bg-white">
                    <td colspan="5" class="py-8 whitespace-nowrap font-semibold text-center text-sm text-gray-700">
                      No entries found.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
            <!-- Pagination -->
            {{ $users->links('livewire.pagination.index') }}
          </div>
        </div>
      </div>
    </div>

    <!-- /End Content -->
  </div>
</div>
