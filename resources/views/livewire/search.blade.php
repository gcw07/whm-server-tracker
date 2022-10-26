<div>
  <!-- Page Header -->
  <div class="relative pb-5 border-b border-gray-200 sm:pb-0">
    <div class="md:flex md:items-center md:justify-between">
      <h3 class="text-2xl leading-6 font-medium text-gray-900">
        Search
      </h3>
    </div>
    <div class="mt-4">

    </div>
  </div>
  <!-- / End Page Header -->

  <div class="mt-6">
    <!-- Begin content -->

    <div class="flex justify-between">
      <div class="max-w-sm sm:max-w-lg w-2/3 sm:w-full">
        <label for="search" class="sr-only">Search</label>
        <div class="relative text-gray-400 focus-within:text-gray-600">
          <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
            <x-heroicon-s-magnifying-glass class="h-5 w-5"/>
          </div>
          <input placeholder="Search" type="text" wire:model.debounce.500ms="q" class="block max-w-lg pl-10 w-full shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm border-gray-300 rounded-md">
        </div>
      </div>
      @if($q)
        <button wire:click="clear" type="button" class="inline-flex items-center justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
          <x-heroicon-s-x-circle class="h-4 w-4 -ml-0.5 mr-1"/>
          Clear filter
        </button>
      @endif

    </div>

    @if($q)
      <div class="mt-4">
        <p class="text-sm">
          {{ $servers->count() }} results for servers matching <b>{{ $q }}</b> sorted by alphabetically.
        </p>
        <p class="text-sm">
          {{ $accounts->count() }} results for accounts matching <b>{{ $q }}</b> sorted by alphabetically.
        </p>
      </div>

      <!-- Server Results -->
      <div class="sm:flex sm:items-center mt-6">
        <div class="sm:flex-auto">
          <h1 class="text-lg font-semibold text-gray-700">Servers</h1>
        </div>
      </div>

      <!-- Server list (smallest breakpoint only) -->
      <div class="shadow sm:hidden">
        <ul role="list" class="mt-2 divide-y divide-gray-200 overflow-hidden shadow sm:hidden">
          @forelse($servers as $server)
            <li>
              <a href="{{ route('servers.show', $server->id) }}"
                @class([
                 'block px-4 py-4 hover:bg-gray-50',
                 'bg-yellow-100' => $server->is_disk_warning,
                 'bg-orange-100' => $server->is_disk_critical,
                 'bg-red-100' => $server->is_disk_full,
                 'bg-gray-50' => $loop->even,
                 'bg-white' => $loop->odd
                ])>
              <span class="flex items-center space-x-4">
                <span class="flex-1 flex space-x-2 truncate">
                  <span class="flex flex-col text-gray-500 text-sm truncate">
                    <span class="truncate text-gray-700 font-semibold">
                      {{ $server->name }}
                    </span>
                    @if($server->missing_token)
                      <span class="inline-flex items-center px-2.5 py-0.5 mt-3 rounded-full text-sm font-medium bg-red-200 text-red-800 capitalize">
                        <x-heroicon-s-exclamation-triangle class="-ml-0.5 mr-1 h-4 w-4" />
                        no token
                      </span>
                    @else
                      <span><span class="font-medium">{{ $server->accounts_count }}</span> accounts</span>
                      <span>{{ $server->settings->get('disk_percentage') }}%</span>
                    @endif
                  </span>
                </span>
                <x-heroicon-s-chevron-right class="flex-shrink-0 h-5 w-5 text-gray-400" />
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
        {{--      {{ $servers->links('livewire.pagination.index') }}--}}
      </div>

      <!-- Server table (small breakpoint and up) -->
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
                  @forelse($servers as $server)
                    <tr @class([
                        'bg-yellow-100' => $server->is_disk_warning,
                        'bg-orange-100' => $server->is_disk_critical,
                        'bg-red-100' => $server->is_disk_full,
                        'bg-gray-50' => $loop->even,
                        'bg-white' => $loop->odd
                    ])>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <div class="flex">
                          <a href="{{ route('servers.show', $server->id) }}" class="group inline-flex space-x-2 truncate text-sm">
                            <p class="text-gray-500 truncate group-hover:text-gray-900">
                              {{ $server->name }}
                            </p>
                            @if($server->missing_token)
                              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-200 text-red-800 capitalize">
                              <x-heroicon-s-exclamation-triangle class="-ml-0.5 mr-1 h-4 w-4" />
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
                        <a href="{{ $server->whm_url }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
                          <x-heroicon-s-arrow-top-right-on-square class="-ml-0.5 h-4 w-4" />
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
              {{--            {{ $servers->links('livewire.pagination.index') }}--}}
            </div>
          </div>
        </div>
      </div>

      <!-- Account Results -->
      <div class="sm:flex sm:items-center mt-8">
        <div class="sm:flex-auto">
          <h1 class="text-lg font-semibold text-gray-700">Accounts</h1>
        </div>
      </div>

      <!-- Accounts list (smallest breakpoint only) -->
      <div class="shadow sm:hidden">
        <ul role="list" class="mt-2 divide-y divide-gray-200 overflow-hidden shadow sm:hidden">
          @forelse($accounts as $account)
            <li>
              <a href="{{ route('accounts.show', $account->id) }}"
                @class([
                 'block px-4 py-4 hover:bg-gray-50',
                 'bg-yellow-100' => $account->is_disk_warning,
                 'bg-orange-100' => $account->is_disk_critical,
                 'bg-red-100' => $account->is_disk_full,
                 'bg-blue-200' => $account->suspended,
                 'bg-gray-50' => $loop->even,
                 'bg-white' => $loop->odd
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
          @empty
            no results
          @endforelse
        </ul>

        <!-- Pagination -->
        {{--      {{ $accounts->links('livewire.pagination.index') }}--}}
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
                      Username
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
                  @forelse($accounts as $account)
                    <tr @class([
                        'bg-yellow-100' => $account->is_disk_warning,
                        'bg-orange-100' => $account->is_disk_critical,
                        'bg-red-100' => $account->is_disk_full,
                        'bg-blue-200' => $account->suspended,
                        'bg-gray-50' => $loop->even,
                        'bg-white' => $loop->odd
                    ])>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <div class="flex items-center space-x-3 lg:pl-2">
                          <div>
                            @if($account->suspended)
                              <x-heroicon-s-no-symbol class="h-5 w-5 text-blue-600" />
                            @elseif($account->is_disk_warning || $account->is_disk_critical || $account->is_disk_full)
                              <x-heroicon-s-exclamation-triangle class="h-5 w-5 text-red-500" />
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
                        {{ $account->user }}
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
                          <x-heroicon-s-arrow-top-right-on-square class="-ml-0.5 h-4 w-4" />
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
              {{--            {{ $accounts->links('livewire.pagination.index') }}--}}
            </div>
          </div>
        </div>
      </div>

      <!-- Monitor Results -->
      <div class="sm:flex sm:items-center mt-8">
        <div class="sm:flex-auto">
          <h1 class="text-lg font-semibold text-gray-700">Monitors</h1>
        </div>
      </div>

      <!-- Monitor list (smallest breakpoint only) -->
      <div class="shadow sm:hidden">
        <ul role="list" class="mt-2 divide-y divide-gray-200 overflow-hidden shadow sm:hidden">
          @forelse($monitors as $monitor)
            <li>
              <a href="{{ route('monitors.show', $monitor->id) }}"
                @class([
                 'block px-4 py-4 hover:bg-gray-50',
                 'bg-gray-50' => $loop->even,
                 'bg-white' => $loop->odd
                ])>
              <span class="flex items-center space-x-4">
                <span class="flex-1 flex space-x-2 truncate">
                  <span class="flex flex-col text-gray-500 text-sm truncate">
                    <span class="truncate text-gray-700 font-semibold">
                      {{ preg_replace("(^https?://)", "", $monitor->url ) }}
                    </span>
                    @if(!$monitor->uptime_check_enabled)
                      <span class="font-medium">Uptime - <span class="font-normal">Disabled</span></span>
                    @else
                      @if($monitor->uptime_status === 'down')
                        <span class="font-medium">Uptime - <span class="font-normal">Down</span></span>
                      @elseif($monitor->uptime_status === 'not yet checked')
                        <span class="font-medium">Uptime - <span class="font-normal">Pending</span></span>
                      @else
                        <span class="font-medium">Uptime - <span class="font-normal">Up</span></span>
                      @endif
                    @endif

                    @if(!$monitor->certificate_check_enabled)
                      <span class="font-medium">Certificate - <span class="font-normal">Disabled</span></span>
                    @else
                      @if($monitor->certificate_status === 'invalid')
                        <span class="font-medium">Certificate - <span class="font-normal">Invalid</span></span>
                      @elseif($monitor->certificate_status === 'not yet checked')
                        <span class="font-medium">Certificate - <span class="font-normal">Pending</span></span>
                      @else
                        <span class="font-medium">Certificate - <span class="font-normal">Ok</span></span>
                      @endif
                    @endif
                  </span>
                </span>
                <x-heroicon-s-chevron-right class="flex-shrink-0 h-5 w-5 text-gray-400" />
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
{{--        {{ $monitors->links('livewire.pagination.index') }}--}}
      </div>

      <!-- Monitor table (small breakpoint and up) -->
      <div class="hidden sm:block">
        <div class="mx-auto">
          <div class="flex flex-col mt-2">
            <div class="align-middle min-w-full overflow-x-auto shadow overflow-hidden sm:rounded-lg">
              <table class="min-w-full divide-y divide-gray-200">
                <thead>
                  <tr>
                    <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Site
                    </th>
                    <th scope="col" class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Uptime
                    </th>
                    <th scope="col" class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider lg:table-cell">
                      Certificate
                    </th>
                    <th scope="col" class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                      <span class="sr-only">Manage</span>
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  @forelse($monitors as $monitor)
                    <tr @class([
                        'bg-gray-50' => $loop->even,
                        'bg-white' => $loop->odd
                    ])>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <div class="flex">
                          <a href="{{ route('monitors.show', $monitor->id) }}" class="group inline-flex space-x-2 truncate text-sm">
                            <p class="text-gray-500 truncate font-semibold group-hover:text-gray-900">
                              {{ preg_replace("(^https?://)", "", $monitor->url ) }}
                            </p>
                          </a>
                        </div>
                      </td>
                      <td class="px-6 py-4 text-center whitespace-nowrap text-sm text-gray-500">
                        @if(!$monitor->uptime_check_enabled)
                          <div class="inline-flex items-center rounded-md py-2 px-3 text-sm font-medium">
                            <x-heroicon-s-no-symbol class="-ml-0.5 mr-2 h-4 w-4 text-blue-600" />
                            <span class="text-gray-900 font-medium">Disabled</span>
                          </div>
                        @else
                          @if($monitor->uptime_status === 'down')
                            <div class="inline-flex items-center rounded-md py-2 px-3 text-sm font-medium">
                              <x-heroicon-s-x-circle class="-ml-0.5 mr-2 h-4 w-4 text-red-600" />
                              <span class="text-gray-900 font-medium">Down</span>
                            </div>
                          @elseif($monitor->uptime_status === 'not yet checked')
                            <div class="inline-flex items-center rounded-md py-2 px-3 text-sm font-medium">
                              <x-heroicon-s-exclamation-triangle class="-ml-0.5 mr-2 h-4 w-4 text-yellow-600" />
                              <span class="text-gray-900 font-medium">Pending</span>
                            </div>
                          @else
                            <div class="inline-flex items-center rounded-md py-2 px-3 text-sm font-medium">
                              <x-heroicon-s-check-circle class="-ml-0.5 mr-2 h-4 w-4 text-green-600" />
                              <span class="text-gray-900 font-medium">Up</span>
                            </div>
                          @endif
                        @endif
                      </td>
                      <td class="px-6 py-4 text-center whitespace-nowrap text-sm text-gray-500">
                        @if(!$monitor->certificate_check_enabled)
                          <div class="inline-flex items-center rounded-md py-2 px-3 text-sm font-medium">
                            <x-heroicon-s-no-symbol class="-ml-0.5 mr-2 h-4 w-4 text-blue-600" />
                            <span class="text-gray-900 font-medium">Disabled</span>
                          </div>
                        @else
                          @if($monitor->certificate_status === 'invalid')
                            <div class="inline-flex items-center rounded-md py-2 px-3 text-sm font-medium">
                              <x-heroicon-s-x-circle class="-ml-0.5 mr-2 h-4 w-4 text-red-600" />
                              <span class="text-gray-900 font-medium">Invalid</span>
                            </div>
                          @elseif($monitor->certificate_status === 'not yet checked')
                            <div class="inline-flex items-center rounded-md py-2 px-3 text-sm font-medium">
                              <x-heroicon-s-exclamation-triangle class="-ml-0.5 mr-2 h-4 w-4 text-yellow-600" />
                              <span class="text-gray-900 font-medium">Pending</span>
                            </div>
                          @else
                            <div class="inline-flex items-center rounded-md py-2 px-3 text-sm font-medium">
                              <x-heroicon-s-check-circle class="-ml-0.5 mr-2 h-4 w-4 text-green-600" />
                              <span class="text-gray-900 font-medium">Ok</span>
                            </div>
                          @endif
                        @endif
                      </td>
                      <td class="px-6 py-4 text-right whitespace-nowrap text-sm text-gray-500">
                        <a href="{{ $monitor->url }}" target="_blank" x-data="{}" x-tooltip.raw="Visit Site" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
                          <x-heroicon-m-arrow-top-right-on-square class="-ml-0.5 h-4 w-4" />
                        </a>
                      </td>
                    </tr>
                  @empty
                    <tr class="bg-white">
                      <td colspan="4" class="py-8 whitespace-nowrap font-semibold text-center text-sm text-gray-700">
                        No entries found.
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
              <!-- Pagination -->
{{--              {{ $monitors->links('livewire.pagination.index') }}--}}
            </div>
          </div>
        </div>
      </div>

    @else
      <div class="text-center mt-10">Start typing above to search server and account listings.</div>
    @endif

    <!-- /End Content -->
  </div>
</div>
