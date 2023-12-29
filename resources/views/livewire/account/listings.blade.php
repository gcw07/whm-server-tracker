<div>
  <!-- Page Header -->
  <div class="pb-3 border-b border-gray-200 sm:flex sm:items-center sm:justify-between">
    <h3 class="text-2xl leading-6 font-medium text-gray-900">
      Accounts
    </h3>
    <div class="mt-3 flex md:mt-0">
      <!-- Sort menu dropdown -->
      <x-navigation.dropdown>
        <x-slot name="trigger">
          <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500" aria-expanded="false" aria-haspopup="true">
            <x-heroicon-s-bars-arrow-down class="-ml-0.5 mr-2 h-4 w-4" />
            Sort
          </button>
        </x-slot>

        <div
          class="origin-top-left z-50 absolute md:origin-top-right md:right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
          role="menu" aria-orientation="vertical" aria-labelledby="sort-menu-button" tabindex="-1">
          <button wire:click.prevent="sortListingsBy(null)" class="w-full flex items-center group px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  role="menuitem" tabindex="-1" id="sort-menu-item-0">
            Alphabetically
          </button>
          <button wire:click.prevent="sortListingsBy('newest')" class="w-full flex items-center group px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  role="menuitem" tabindex="-1" id="sort-menu-item-1">
            Newest
          </button>
          <button wire:click.prevent="sortListingsBy('usage_high')" class="w-full flex items-center group px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  role="menuitem" tabindex="-1" id="sort-menu-item-3">
            Usage: High to Low
          </button>
          <button wire:click.prevent="sortListingsBy('usage_low')" class="w-full flex items-center group px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  role="menuitem" tabindex="-1" id="sort-menu-item-4">
            Usage: Low to High
          </button>
        </div>
      </x-navigation.dropdown>

      <!-- Filters menu dropdown -->
      <x-navigation.dropdown class="ml-2">
        <x-slot name="trigger">
          <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500" aria-expanded="false" aria-haspopup="true">
            <x-heroicon-s-funnel class="-ml-0.5 mr-2 h-4 w-4" />
            Filters
          </button>
        </x-slot>

        <div
          class="origin-top-left z-50 absolute md:origin-top-right md:right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
          role="menu" aria-orientation="vertical" aria-labelledby="filters-menu-button" tabindex="-1">
          <button wire:click.prevent="filterListingsBy(null)" class="w-full flex items-center group px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  role="menuitem" tabindex="-1" id="filters-menu-item-0">
            None
          </button>
          <button wire:click.prevent="filterListingsBy('duplicates')" class="w-full flex items-center group px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  role="menuitem" tabindex="-1" id="filters-menu-item-1">
            Duplicates
          </button>
        </div>
      </x-navigation.dropdown>

      <button wire:click='$dispatch("openModal", "account.export", {{ json_encode(["sortBy" => $sortBy]) }})' type="button" class="ml-2 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500" aria-expanded="false" aria-haspopup="true">
        <x-heroicon-s-arrow-down-tray class="-ml-0.5 mr-2 h-4 w-4" />
        Export
      </button>
    </div>
  </div>
  <!-- / End Page Header -->

  <div class="mt-6">
    <!-- Begin content -->

    <!-- Accounts list (smallest breakpoint only) -->
    <div class="shadow sm:hidden">
      <ul role="list" class="mt-2 divide-y divide-gray-200 overflow-hidden shadow sm:hidden">
        @foreach($accounts as $account)
        <li>
          <a href="{{ route('accounts.show', $account->id) }}"
            @class([
             'block px-4 py-4 hover:bg-gray-50',
             'bg-yellow-100' => $account->is_disk_warning,
             'bg-orange-100' => $account->is_disk_critical,
             'bg-red-100' => $account->is_disk_full,
             'bg-blue-200' => $account->suspended,
             'bg-gray-50' => $loop->even && !($account->is_disk_warning || $account->is_disk_critical || $account->is_disk_full || $account->suspended),
             'bg-white' => $loop->odd && !($account->is_disk_warning || $account->is_disk_critical || $account->is_disk_full || $account->suspended)
            ])>
            <span class="flex items-center space-x-4">
              <span class="flex-1 flex space-x-2 truncate">
                <span class="flex flex-col text-gray-500 text-sm truncate">
                  <span class="flex items-center truncate space-x-3">
                    @if($account->suspended)
                      <x-heroicon-s-no-symbol class="h-5 w-5 text-blue-600" />
                    @elseif($account->is_disk_warning || $account->is_disk_critical || $account->is_disk_full)
                      <x-heroicon-s-exclamation-triangle class="h-5 w-5 text-red-500" />
                    @else
                      <span class="w-3 h-3 m-1 flex-shrink-0 rounded-full bg-green-600" aria-hidden="true"></span>
                    @endif
                    <span class="text-gray-900 font-medium truncate">{{ $account->domain }}</span>
                  </span>
                  <span class="truncate">{{ $account->server->name }}</span>
                  @if($account->formatted_disk_usage === 'Unknown')
                    <span>&mdash;</span>
                  @else
                    <span class="flex items-center">
                      <span>{{ $account->formatted_disk_usage }}</span>
                    </span>
                  @endif
                </span>
              </span>
              <x-heroicon-s-chevron-right class="flex-shrink-0 h-5 w-5 text-gray-400" />
            </span>
          </a>
        </li>
        @endforeach
      </ul>

      <!-- Pagination -->
      {{ $accounts->links('livewire.pagination.index') }}
    </div>

    <!-- Accounts table (small breakpoint and up) -->
    <div class="hidden sm:block">
      <div class="mx-auto">
        <div class="flex flex-col mt-2">
          <div class="align-middle min-w-full overflow-x-auto shadow overflow-hidden sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
              <thead>
                <tr>
                  <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Domain
                  </th>
                  <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Server
                  </th>
                  <th scope="col" class="hidden px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider lg:table-cell">
                    WordPress
                  </th>
                  <th scope="col" class="hidden px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider lg:table-cell">
                    Backups
                  </th>
                  <th scope="col" class="hidden px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider lg:table-cell">
                    Used / Limit
                  </th>
                  <th scope="col" class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Usage
                  </th>
                  <th scope="col" class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <span class="sr-only">Manage</span>
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                @foreach($accounts as $account)
                  <tr @class([
                        'bg-yellow-100' => $account->is_disk_warning,
                        'bg-orange-100' => $account->is_disk_critical,
                        'bg-red-100' => $account->is_disk_full,
                        'bg-blue-200' => $account->suspended,
                        'bg-gray-50' => $loop->even && !($account->is_disk_warning || $account->is_disk_critical || $account->is_disk_full || $account->suspended),
                        'bg-white' => $loop->odd && !($account->is_disk_warning || $account->is_disk_critical || $account->is_disk_full || $account->suspended)
                    ])>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      <div class="flex items-center space-x-3 lg:pl-2">
                        <div x-data="{}">
                          @if($account->suspended)
                            <x-heroicon-s-no-symbol class="h-5 w-5 text-blue-600" x-tooltip.raw="Account Suspended" />
                          @elseif($account->is_disk_warning || $account->is_disk_critical || $account->is_disk_full)
                            <x-heroicon-s-exclamation-triangle class="h-5 w-5 text-red-500" x-tooltip.raw="Disk Warning" />
                          @else
                            <div class="flex-shrink-0 w-3 h-3 m-1 rounded-full bg-green-600" aria-hidden="true"></div>
                          @endif
                        </div>
                        <a href="{{ route('accounts.show', $account->id) }}" class="group inline-flex space-x-2 truncate text-sm">
                          <p class="text-gray-500 truncate group-hover:text-gray-900">
                            {{ $account->domain }}
                          </p>
                        </a>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      <div class="flex">
                        <a href="{{ route('servers.show', $account->server->id) }}" class="group inline-flex space-x-2 truncate text-sm">
                          <p class="text-gray-500 truncate group-hover:text-gray-900">
                            {{ $account->server->name }}
                          </p>
                        </a>
                      </div>
                    </td>
                    <td class="hidden px-6 py-4 whitespace-nowrap text-sm text-gray-500 lg:table-cell">
                      @if($account->wordpress_version)
                        <span class="text-gray-900 font-medium">{{ $account->wordpress_version }}</span>
                      @else
                        <span class="text-gray-900 font-medium">&mdash;</span>
                      @endif
                    </td>
                    <td class="hidden px-6 py-4 text-center whitespace-nowrap text-sm text-gray-500 lg:table-cell">
                      @if($account->backups_enabled)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-200 text-green-800 capitalize">
                          yes
                        </span>
                      @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-200 text-red-800 capitalize">
                          no
                        </span>
                      @endif
                    </td>
                    <td class="hidden px-6 py-4 text-center whitespace-nowrap text-sm text-gray-500 lg:table-cell">
                      {{ $account->disk_used }} / {{ $account->disk_limit }}
                    </td>
                    <td class="px-6 py-4 text-center whitespace-nowrap text-sm text-gray-500">
                      <div class="flex items-center justify-center">
                        @if($account->formatted_disk_usage === 'Unknown')
                          <span class="text-gray-900 font-medium">&mdash;</span>
                        @else
                          <span class="text-gray-900 font-medium">{{ $account->formatted_disk_usage }}</span>
                        @endif
                      </div>
                    </td>
                    <td class="px-6 py-4 text-right whitespace-nowrap text-sm text-gray-500">
                      <a href="{{ $account->server->whm_url }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
                        <x-heroicon-m-arrow-top-right-on-square class="-ml-0.5 h-4 w-4" />
                      </a>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
            <!-- Pagination -->
            {{ $accounts->links('livewire.pagination.index') }}
          </div>
        </div>
      </div>
    </div>

    <!-- /End Content -->
  </div>
</div>
