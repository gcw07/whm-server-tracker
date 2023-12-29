<div>
  <!-- Page Header -->
  <div class="pb-5 border-b border-gray-200 md:flex md:items-center md:justify-between">
    <div class="flex-1 min-w-0">
      <nav class="flex" aria-label="Breadcrumb">
        <ol role="list" class="flex items-center space-x-4">
          <li>
            <div class="flex">
              <a href="{{ route('servers.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">Servers</a>
            </div>
          </li>
          <li>
            <div class="flex items-center">
              <x-heroicon-s-chevron-right class="flex-shrink-0 h-5 w-5 text-gray-400" />
              <span class="ml-4 text-sm font-medium text-gray-500">{{ $server->formatted_server_type }}</span>
            </div>
          </li>
        </ol>
      </nav>
      <h3 class="mt-2 text-2xl leading-6 font-medium text-gray-900">
        {{ $server->name }}
      </h3>
    </div>

    <div class="flex mt-3 md:mt-0 md:ml-4">
      <a href="{{ $server->whm_url }}" target="_blank" type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
        <x-heroicon-m-arrow-top-right-on-square class="-ml-0.5 mr-2 h-4 w-4" />
        View
      </a>

      @if(!$server->missing_token)
        <button wire:click="refresh" type="button" class="ml-2 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
          <x-heroicon-s-arrow-path class="-ml-0.5 mr-2 h-4 w-4" />
          Refresh
        </button>
      @endif

      <!-- Details menu dropdown -->
      <x-navigation.dropdown class="ml-2">
        <x-slot name="trigger">
          <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500" aria-expanded="false" aria-haspopup="true">
            <x-heroicon-s-ellipsis-vertical class="-ml-0.5 -mr-1 h-4 w-4" />
            &nbsp;
          </button>
        </x-slot>

        <div
          class="origin-top-right z-50 absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
          role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
          <a href="{{ route('servers.edit', $server) }}" class="flex items-center group px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
             role="menuitem" tabindex="-1" id="details-menu-item-0">
            <x-heroicon-s-pencil-square class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" />
            Edit
          </a>
          @if(!$server->missing_token)
            <button wire:click="$dispatch('openModal', { component: 'server.reset-token', arguments: { server: {{ $server->id }} }})" class="w-full flex items-center group px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    role="menuitem" tabindex="-1" id="details-menu-item-1">
              <x-heroicon-s-key class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" />
              Reset API Token
            </button>
          @endif
          <button wire:click="$dispatch('openModal', { component: 'server.delete', arguments: { server: {{ $server->id }} }})" class="w-full flex items-center group px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
             role="menuitem" tabindex="-1" id="details-menu-item-2">
            <x-heroicon-s-trash class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" />
            Delete
          </button>
        </div>
      </x-navigation.dropdown>
    </div>
  </div>
  <!-- / End Page Header -->


  <div class="mt-6">
    <!-- Begin content -->

    @if($server->missing_token)
      <button wire:click="$dispatch('openModal', { component: 'server.new-token', arguments: { server: {{ $server->id }} }})" type="button" class="relative block w-full border-2 border-gray-300 border-dashed rounded-lg p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        <x-heroicon-o-key class="mx-auto h-12 w-12 text-gray-400" />
        <span class="mt-2 block text-sm font-medium text-gray-900"> Add an API token to get started </span>
      </button>

    @else
      <div class="hidden text-sm text-gray-500 sm:flex sm:justify-end">
        Last Updated:
        @if($server->server_update_last_succeeded_at)
          {{ $server->server_update_last_succeeded_at->diffForHumans() }}
        @else
          Never
        @endif
      </div>
      <div class="hidden sm:block">
        <dl class="mt-5 grid grid-cols-1 rounded-lg bg-white overflow-hidden shadow lg:grid-cols-3">
          <!--          <dl class="mt-5 grid grid-cols-1 rounded-lg bg-white overflow-hidden shadow divide-y divide-gray-200 lg:grid-cols-3 lg:divide-x lg:divide-y-0">-->
          <div class="lg:border-r lg:border-gray-200">
            <dt class="bg-gray-50 border-b border-gray-200 text-lg p-5 font-normal text-gray-900 flex items-center">
              <div class="bg-sky-500 rounded-md p-1 mr-2">
                <x-heroicon-s-information-circle class="h-5 w-5 text-white" />
              </div>
              Details
            </dt>
            <dl class="sm:divide-y sm:divide-gray-200 sm:px-5 sm:pt-4 sm:pb-1">
              <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="text-sm font-medium text-gray-400">
                  Accounts
                </dt>
                <dd class="mt-1 text-sm font-semibold text-gray-600 sm:mt-0 sm:col-span-2">
                  {{ $server->accounts_count }}
                </dd>
              </div>
              <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="text-sm font-medium text-gray-400">
                  Server type
                </dt>
                <dd class="mt-1 text-sm font-semibold text-gray-600 sm:mt-0 sm:col-span-2">
                  {{ $server->formatted_server_type }}
                  <span class="text-gray-400 font-medium">&mdash;</span>
                  <span>WHM {{ $server->formatted_whm_version }}</span>
                </dd>
              </div>
              <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="text-sm font-medium text-gray-400">
                  PHP versions
                </dt>
                <dd class="mt-1 text-sm font-semibold text-gray-600 sm:mt-0 sm:col-span-2">
                  @foreach($server->formatted_php_installed_versions as $version)
                    {{ $version }}@if (!$loop->last),@endif
                  @endforeach
                  <span class="text-gray-400 font-medium">&mdash;</span>
                  <span>System {{ $server->formatted_php_system_version }}</span>
                </dd>
              </div>
              <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="text-sm font-medium text-gray-400">
                  Server URL
                </dt>
                <dd class="mt-1 text-sm font-semibold text-gray-600 sm:mt-0 sm:col-span-2">
                  {{ $server->address }}:{{ $server->port }}
                </dd>
              </div>
            </dl>
          </div>

          <div class="lg:border-r lg:border-gray-200">
            <dt class="bg-gray-50 border-b border-gray-200 text-lg p-5 font-normal text-gray-900 flex items-center">
              <div class="bg-sky-500 rounded-md p-1 mr-2">
                <x-heroicon-s-server class="h-5 w-5 text-white" />
              </div>
              Disk
            </dt>
            <dl class="sm:divide-y sm:divide-gray-200 sm:px-5 sm:pt-4 sm:pb-1">
              <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="text-sm font-medium text-gray-400">
                  Usage
                </dt>
                <dd class="mt-1 text-sm font-semibold text-gray-600 sm:mt-0 sm:col-span-2">
                  {{ $server->settings->get('disk_percentage') }}%
                </dd>
              </div>
              <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="text-sm font-medium text-gray-400">
                  Used
                </dt>
                <dd class="mt-1 text-sm font-semibold text-gray-600 sm:mt-0 sm:col-span-2">
                  {{ $server->formatted_disk_used }}
                </dd>
              </div>
              <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="text-sm font-medium text-gray-400">
                  Available
                </dt>
                <dd class="mt-1 text-sm font-semibold text-gray-600 sm:mt-0 sm:col-span-2">
                  {{ $server->formatted_disk_available }}
                </dd>
              </div>
              <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="text-sm font-medium text-gray-400">
                  Total
                </dt>
                <dd class="mt-1 text-sm font-semibold text-gray-600 sm:mt-0 sm:col-span-2">
                  {{ $server->formatted_disk_total }}
                </dd>
              </div>
            </dl>
          </div>

          <div>
            <dt class="bg-gray-50 border-b border-gray-200 text-lg p-5 font-normal text-gray-900 flex items-center">
              <div class="bg-sky-500 rounded-md p-1 mr-2">
                <x-heroicon-s-archive-box class="h-5 w-5 text-white" />
              </div>
              Backups
            </dt>
            <dl class="sm:divide-y sm:divide-gray-200 sm:px-5 sm:pt-4 sm:pb-1">
              <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="text-sm font-medium text-gray-400">
                  Enabled
                </dt>
                <dd class="mt-1 text-sm font-semibold text-gray-600 sm:mt-0 sm:col-span-2">
                  @if($server->backups_enabled)
                    Yes
                  @else
                    No
                  @endif
                </dd>
              </div>
              <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="text-sm font-medium text-gray-400">
                  Daily
                </dt>
                <dd class="mt-1 text-sm font-semibold text-gray-600 sm:mt-0 sm:col-span-2">
                  @if($server->settings->get('backup_daily_enabled'))
                    {{ $server->settings->get('backup_daily_retention') }} / {{ $server->formatted_backup_daily_days }}
                  @else
                    Disabled
                  @endif
                </dd>
              </div>
              <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="text-sm font-medium text-gray-400">
                  Weekly
                </dt>
                <dd class="mt-1 text-sm font-semibold text-gray-600 sm:mt-0 sm:col-span-2">
                  @if($server->settings->get('backup_weekly_enabled'))
                    {{ $server->settings->get('backup_weekly_retention') }} / {{ $server->formatted_backup_weekly_day }}
                  @else
                    Disabled
                  @endif
                </dd>
              </div>
              <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="text-sm font-medium text-gray-400">
                  Monthly
                </dt>
                <dd class="mt-1 text-sm font-semibold text-gray-600 sm:mt-0 sm:col-span-2">
                  @if($server->settings->get('backup_monthly_enabled'))
                    {{ $server->settings->get('backup_monthly_retention') }} / {{ $server->formatted_backup_monthly_days }}
                  @else
                    Disabled
                  @endif
                </dd>
              </div>
            </dl>
          </div>
          <div class="col-span-1 lg:col-span-3 text-sm font-normal text-gray-600 p-4 flex sm:border-t sm:border-gray-200">
            <span class="font-semibold mr-4">Notes</span>
            <p>{{ $server->notes }}</p>
          </div>
        </dl>
      </div>

      <!-- Accounts list (smallest breakpoint only) -->
      <div class="shadow sm:hidden mt-8">
        <ul role="list" class="mt-2 divide-y divide-gray-200 overflow-hidden shadow sm:hidden rounded-lg">
          @forelse($server->accounts as $account)
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
          @empty
            <li>
            <span class="block px-4 py-4 bg-white hover:bg-gray-50">
              No accounts found.
            </span>
            </li>
          @endforelse
        </ul>
      </div>

      <!-- Accounts table (small breakpoint and up) -->
      <div class="hidden sm:block mt-8">
        <div class="mx-auto">
          <div class="flex flex-col mt-2">
            <div class="align-middle min-w-full overflow-x-auto shadow overflow-hidden sm:rounded-lg">
              <table class="min-w-full divide-y divide-gray-200">
                <thead>
                  <tr>
                    <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Domain
                    </th>
                    <th scope="col" class="hidden px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider lg:table-cell">
                      WordPress
                    </th>
                    <th scope="col" class="hidden px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider lg:table-cell">
                      Backups
                    </th>
                    <th scope="col" class="hidden px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider lg:table-cell">
                      Plan
                    </th>
                    <th scope="col" class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
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
                  @forelse($server->accounts as $account)
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
                      <td class="hidden px-6 py-4 whitespace-nowrap text-sm text-gray-900 lg:table-cell">
                        <div class="flex">
                          {{ $account->plan }}
                        </div>
                      </td>
                      <td class="px-6 py-4 text-center whitespace-nowrap text-sm text-gray-500">
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
                        <a href="{{ $account->server->whm_url }}" target="_blank" x-data="{}" x-tooltip.raw="View WHM Panel" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
                          <x-heroicon-m-arrow-top-right-on-square class="-ml-0.5 h-4 w-4" />
                        </a>
                      </td>
                    </tr>
                  @empty
                    <tr class="bg-white">
                      <td colspan="7" class="py-8 whitespace-nowrap font-semibold text-center text-sm text-gray-700">
                        No accounts found.
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    @endif

    <!-- /End Content -->
  </div>

</div>
