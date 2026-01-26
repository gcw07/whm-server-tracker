<div>
  <!-- Page Header -->
  <div class="relative pb-5 border-b border-gray-200 sm:pb-0">
    <div class="md:flex md:items-center md:justify-between">
      <h3 class="text-2xl leading-6 font-medium text-gray-900">
        Servers
      </h3>
      <div class="mt-3 flex md:mt-0 md:absolute md:top-3 md:right-0">
        <!-- Sort menu dropdown -->
        {{--        <x-navigation.dropdown>--}}
        {{--          <x-slot name="trigger">--}}
        {{--            <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500" aria-expanded="false" aria-haspopup="true">--}}
        {{--              <x-heroicon-s-bars-arrow-down class="-ml-0.5 mr-2 h-4 w-4" />--}}
        {{--              Sort--}}
        {{--            </button>--}}
        {{--          </x-slot>--}}

        {{--          <div--}}
        {{--            class="origin-top-left z-50 absolute md:origin-top-right md:right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"--}}
        {{--            role="menu" aria-orientation="vertical" aria-labelledby="sort-menu-button" tabindex="-1">--}}
        {{--            <button wire:click.prevent="sortListingsBy(null)" class="w-full flex items-center group px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"--}}
        {{--                    role="menuitem" tabindex="-1" id="sort-menu-item-0">--}}
        {{--              Alphabetically--}}
        {{--            </button>--}}
        {{--            <button wire:click.prevent="sortListingsBy('newest')" class="w-full flex items-center group px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"--}}
        {{--                    role="menuitem" tabindex="-1" id="sort-menu-item-1">--}}
        {{--              Newest--}}
        {{--            </button>--}}
        {{--            <button wire:click.prevent="sortListingsBy('accounts')" class="w-full flex items-center group px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"--}}
        {{--                    role="menuitem" tabindex="-1" id="sort-menu-item-2">--}}
        {{--              # of Accounts--}}
        {{--            </button>--}}
        {{--            <button wire:click.prevent="sortListingsBy('usage_high')" class="w-full flex items-center group px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"--}}
        {{--                    role="menuitem" tabindex="-1" id="sort-menu-item-3">--}}
        {{--              Usage: High to Low--}}
        {{--            </button>--}}
        {{--            <button wire:click.prevent="sortListingsBy('usage_low')" class="w-full flex items-center group px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"--}}
        {{--                    role="menuitem" tabindex="-1" id="sort-menu-item-4">--}}
        {{--              Usage: Low to High--}}
        {{--            </button>--}}
        {{--          </div>--}}
        {{--        </x-navigation.dropdown>--}}

        <!-- Filters menu dropdown -->
        {{--        <x-navigation.dropdown class="ml-2">--}}
        {{--          <x-slot name="trigger">--}}
        {{--            <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500" aria-expanded="false" aria-haspopup="true">--}}
        {{--              <x-heroicon-s-funnel class="-ml-0.5 mr-2 h-4 w-4" />--}}
        {{--              Filters--}}
        {{--            </button>--}}
        {{--          </x-slot>--}}

        {{--          <div--}}
        {{--            class="origin-top-left z-50 absolute md:origin-top-right md:right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"--}}
        {{--            role="menu" aria-orientation="vertical" aria-labelledby="filters-menu-button" tabindex="-1">--}}
        {{--            <button wire:click.prevent="filterListingsBy(null)" class="w-full flex items-center group px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"--}}
        {{--                    role="menuitem" tabindex="-1" id="filters-menu-item-0">--}}
        {{--              None--}}
        {{--            </button>--}}
        {{--            <button wire:click.prevent="filterListingsBy('no_backups')" class="w-full flex items-center group px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"--}}
        {{--                    role="menuitem" tabindex="-1" id="filters-menu-item-1">--}}
        {{--              No Backups--}}
        {{--            </button>--}}
        {{--            <button wire:click.prevent="filterListingsBy('outdated_php')" class="w-full flex items-center group px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"--}}
        {{--                    role="menuitem" tabindex="-1" id="filters-menu-item-1">--}}
        {{--              Outdated PHP--}}
        {{--            </button>--}}
        {{--          </div>--}}
        {{--        </x-navigation.dropdown>--}}

        <a href="{{ route('servers.create') }}" class="ml-2 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
          {{--          <x-heroicon-s-plus class="-ml-0.5 mr-2 h-4 w-4" />--}}
          Create Server
        </a>
      </div>
    </div>
    <div class="mt-4">
      <flux:tab.group>
        <flux:tabs wire:model.live="serverType">
          <flux:tab name="all" class="hover:border-gray-300">All</flux:tab>
          <flux:tab name="dedicated" class="hover:border-gray-300">Dedicated</flux:tab>
          <flux:tab name="reseller" class="hover:border-gray-300">Reseller</flux:tab>
          <flux:tab name="vps" class="hover:border-gray-300">VPS</flux:tab>
        </flux:tabs>

        <flux:tab.panel name="all">
          <flux:table :paginate="$this->servers">
            <flux:table.columns>
              <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">Name</flux:table.column>
              <flux:table.column>&nbsp;</flux:table.column>
              <flux:table.column sortable :sorted="$sortBy === 'accounts'" :direction="$sortDirection" wire:click="sort('accounts')">Accounts</flux:table.column>
              <flux:table.column>Backups</flux:table.column>
              <flux:table.column>PHP</flux:table.column>
              <flux:table.column sortable :sorted="$sortBy === 'usage'" :direction="$sortDirection" wire:click="sort('usage')">Usage</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
              @foreach ($this->servers as $server)
                <flux:table.row :key="$server->id">
                  <flux:table.cell class="flex items-center gap-3">
                    {{ $server->name }}
                  </flux:table.cell>

                  <flux:table.cell class="whitespace-nowrap">
                    <flux:badge size="sm" inset="top bottom">{{ $server->formatted_server_type }}</flux:badge>
                  </flux:table.cell>

                  <flux:table.cell class="whitespace-nowrap">{{ $server->accounts_count }}</flux:table.cell>

                  <flux:table.cell>
                    <flux:badge size="sm" :color="$server->backups_enabled ? 'green' : 'red'" inset="top bottom">{{ $server->backups_enabled ? 'Yes' : 'No'}}</flux:badge>
                  </flux:table.cell>

                  <flux:table.cell>
                    @foreach($server->formatted_php_installed_versions as $version)
                      <flux:badge size="sm" :color="$server->backups_enabled ? 'green' : 'red'" inset="top bottom">{{ $version }}</flux:badge>

{{--                      <span @class([--}}
{{--                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize',--}}
{{--                                'bg-red-200 text-red-800' => $server->isPhpVersionEndOfLife($version),--}}
{{--                                'bg-amber-300 text-amber-700' => $server->isPhpVersionSecurityOnly($version),--}}
{{--                                'bg-green-200 text-green-800' => $server->isPhpVersionActive($version),--}}
{{--                              ])>--}}
{{--                          {{ $version }}--}}
{{--                        </span>--}}
                    @endforeach
                  </flux:table.cell>

                  <flux:table.cell class="whitespace-nowrap">{{ $server->settings->get('disk_percentage') }}%</flux:table.cell>

                  <flux:table.cell>
                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>
                  </flux:table.cell>
                </flux:table.row>
              @endforeach
            </flux:table.rows>
          </flux:table>





        </flux:tab.panel>
        <flux:tab.panel name="dedicated">

        </flux:tab.panel>
        <flux:tab.panel name="reseller">

        </flux:tab.panel>
        <flux:tab.panel name="vps">
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
                        <th scope="col" class="hidden px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider lg:table-cell">
                          <span class="sr-only">Type</span>
                        </th>
                        <th scope="col" class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Accounts
                        </th>
                        <th scope="col" class="hidden px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider lg:table-cell">
                          Backups
                        </th>
                        <th scope="col" class="hidden px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider lg:table-cell">
                          PHP
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
                      @forelse($this->servers as $server)
                        <tr @class([
                        'bg-yellow-100' => $server->is_disk_warning,
                        'bg-orange-100' => $server->is_disk_critical,
                        'bg-red-100' => $server->is_disk_full,
                        'bg-gray-50' => $loop->even && !($server->is_disk_warning || $server->is_disk_critical || $server->is_disk_full),
                        'bg-white' => $loop->odd && !($server->is_disk_warning || $server->is_disk_critical || $server->is_disk_full)
                    ])>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="flex">
                              <a href="{{ route('servers.show', $server->id) }}" class="group inline-flex space-x-2 truncate text-sm">
                                <p class="text-gray-500 truncate font-semibold group-hover:text-gray-900">
                                  {{ $server->name }}
                                </p>
                                @if($server->missing_token)
                                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-200 text-red-800 capitalize">
{{--                              <x-heroicon-s-exclamation-triangle class="-ml-0.5 mr-1 h-4 w-4" />--}}
                              no token
                            </span>
                                @endif
                              </a>
                            </div>
                          </td>
                          <td class="hidden px-6 py-4 whitespace-nowrap text-sm text-gray-500 lg:table-cell">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-800 capitalize">
                        {{ $server->formatted_server_type }}
                      </span>
                          </td>
                          <td class="px-6 py-4 text-center whitespace-nowrap text-sm text-gray-500">
                            <span class="text-gray-900 font-medium">{{ $server->accounts_count }}</span>
                          </td>
                          <td class="hidden px-6 py-4 text-center whitespace-nowrap text-sm text-gray-500 lg:table-cell">
                            @if($server->backups_enabled)
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
                            @foreach($server->formatted_php_installed_versions as $version)
                              <span @class([
                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize',
                                'bg-red-200 text-red-800' => $server->isPhpVersionEndOfLife($version),
                                'bg-amber-300 text-amber-700' => $server->isPhpVersionSecurityOnly($version),
                                'bg-green-200 text-green-800' => $server->isPhpVersionActive($version),
                              ])>
                          {{ $version }}
                        </span>
                            @endforeach
                          </td>
                          <td class="px-6 py-4 text-center whitespace-nowrap text-sm text-gray-500">
                            <span class="text-gray-900 font-medium">{{ $server->settings->get('disk_percentage') }}%</span>
                          </td>
                          <td class="px-6 py-4 text-right whitespace-nowrap text-sm text-gray-500">
                            <a href="{{ $server->whm_url }}" target="_blank" x-data="{}" x-tooltip.raw="View WHM Panel" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
                              {{--                        <x-heroicon-m-arrow-top-right-on-square class="-ml-0.5 h-4 w-4" />--}}
                            </a>
                          </td>
                        </tr>
                      @empty
                        <tr class="bg-white">
                          <td colspan="7" class="py-8 whitespace-nowrap font-semibold text-center text-sm text-gray-700">
                            No entries found.
                          </td>
                        </tr>
                      @endforelse
                    </tbody>
                  </table>
                  <!-- Pagination -->
                  {{ $this->servers->links('livewire.pagination.index') }}
                </div>
              </div>
            </div>
          </div>
        </flux:tab.panel>
      </flux:tab.group>
    </div>
  </div>
  <!-- / End Page Header -->

{{--  <div class="mt-6">--}}
{{--    <!-- Begin content -->--}}

{{--    <!-- Server list (smallest breakpoint only) -->--}}
{{--    <div class="shadow sm:hidden">--}}
{{--      <ul role="list" class="mt-2 divide-y divide-gray-200 overflow-hidden shadow sm:hidden">--}}
{{--        @forelse($this->servers as $server)--}}
{{--          <li>--}}
{{--            <a href="{{ route('servers.show', $server->id) }}"--}}
{{--              @class([--}}
{{--               'block px-4 py-4 hover:bg-gray-50',--}}
{{--               'bg-yellow-100' => $server->is_disk_warning,--}}
{{--               'bg-orange-100' => $server->is_disk_critical,--}}
{{--               'bg-red-100' => $server->is_disk_full,--}}
{{--               'bg-gray-50' => $loop->even && !($server->is_disk_warning || $server->is_disk_critical || $server->is_disk_full),--}}
{{--               'bg-white' => $loop->odd && !($server->is_disk_warning || $server->is_disk_critical || $server->is_disk_full)--}}
{{--              ])>--}}
{{--              <span class="flex items-center space-x-4">--}}
{{--                <span class="flex-1 flex space-x-2 truncate">--}}
{{--                  <span class="flex flex-col text-gray-500 text-sm truncate">--}}
{{--                    <span class="truncate text-gray-700 font-semibold">--}}
{{--                      {{ $server->name }}--}}
{{--                    </span>--}}
{{--                    @if($server->missing_token)--}}
{{--                      <span class="inline-flex items-center px-2.5 py-0.5 mt-3 rounded-full text-sm font-medium bg-red-200 text-red-800 capitalize">--}}
{{--                        <x-heroicon-s-exclamation-triangle class="-ml-0.5 mr-1 h-4 w-4" />--}}
{{--                        no token--}}
{{--                      </span>--}}
{{--                    @else--}}
{{--                      <span><span class="font-medium">{{ $server->accounts_count }}</span> accounts</span>--}}
{{--                      <span>{{ $server->settings->get('disk_percentage') }}%</span>--}}
{{--                    @endif--}}
{{--                  </span>--}}
{{--                </span>--}}
{{--                <x-heroicon-s-chevron-right class="shrink-0 h-5 w-5 text-gray-400" />--}}
{{--              </span>--}}
{{--            </a>--}}
{{--          </li>--}}
{{--        @empty--}}
{{--          <li>--}}
{{--            <span class="block px-4 py-4 bg-white hover:bg-gray-50">--}}
{{--              No entries found.--}}
{{--            </span>--}}
{{--          </li>--}}
{{--        @endforelse--}}
{{--      </ul>--}}

{{--      <!-- Pagination -->--}}
{{--      {{ $this->servers->links('livewire.pagination.index') }}--}}
{{--    </div>--}}

{{--    <!-- Server table (small breakpoint and up) -->--}}


{{--    <!-- /End Content -->--}}
{{--  </div>--}}
</div>
