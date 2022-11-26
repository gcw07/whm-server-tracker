<div>
  <!-- Page Header -->
  <div class="pb-5 border-b border-gray-200 md:flex md:items-center md:justify-between">
    <div class="flex-1 min-w-0">
      <nav class="flex" aria-label="Breadcrumb">
        <ol role="list" class="flex items-center space-x-4">
          <li>
            <div class="flex">
              <a href="{{ route('monitors.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">Monitors</a>
            </div>
          </li>
          <li>
            <div class="flex items-center">
              <x-heroicon-s-chevron-right class="flex-shrink-0 h-5 w-5 text-gray-400" />
              <span class="ml-4 text-sm font-medium text-gray-500">Details</span>
            </div>
          </li>
        </ol>
      </nav>
      <h3 class="mt-2 text-2xl leading-6 font-medium text-gray-900">
        {{ preg_replace("(^https?://)", "", $monitor->url ) }}
      </h3>
    </div>

    <div class="flex mt-3 md:mt-0 md:ml-4">
      <a href="{{ $monitor->url }}" target="_blank" type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
        <x-heroicon-m-arrow-top-right-on-square class="-ml-0.5 mr-2 h-4 w-4" />
        View
      </a>

      <button wire:click="refreshCertificateCheck" type="button" class="ml-2 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
        <x-heroicon-s-arrow-path class="-ml-0.5 mr-2 h-4 w-4" />
        Refresh
      </button>
    </div>
  </div>
  <!-- / End Page Header -->


  <div class="mt-6">
    <!-- Begin content -->

    <div>
      <dl class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-2">
        <div class="bg-white shadow rounded-lg divide-y divide-gray-200">
          <div class="bg-gray-50 rounded-lg px-4 py-5 sm:px-6">
            <div class="-ml-4 -mt-4 flex justify-between items-center flex-wrap sm:flex-nowrap">
              <div class="ml-4 mt-4 flex items-center">
                @if($monitor->uptime_check_enabled)
                  <div @class([
                   'flex-shrink-0 w-3 h-3 m-1 mr-2 rounded-full ',
                   'bg-green-600' => $monitor->uptime_status === 'up',
                   'bg-red-600' => $monitor->uptime_status === 'down',
                   'bg-yellow-600' => $monitor->uptime_status === 'not yet checked'
                    ]) aria-hidden="true"></div>
                @else
                  <div class="flex-shrink-0 w-3 h-3 m-1 mr-2 rounded-full bg-gray-50 border border-black" aria-hidden="true"></div>
                @endif
                <h3 class="text-lg leading-6 font-medium text-gray-900">Uptime</h3>
              </div>
              <div class="ml-4 mt-4 flex-shrink-0">
                <!-- Uptime menu dropdown -->
                <x-navigation.dropdown>
                  <x-slot name="trigger">
                    <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500" aria-expanded="false" aria-haspopup="true">
                      <x-heroicon-s-ellipsis-vertical class="-ml-0.5 -mr-1 h-4 w-4" />
                      &nbsp;
                    </button>
                  </x-slot>

                  <div
                    class="origin-top-right z-50 absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                    role="menu" aria-orientation="vertical" aria-labelledby="uptime-menu-button" tabindex="-1">
                    <button wire:click="toggleUptimeCheck" class="w-full flex items-center group px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            role="menuitem" tabindex="-1" id="uptime-menu-item-0">
                      @if($monitor->uptime_check_enabled)
                        <x-heroicon-s-no-symbol class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" />
                        Disable
                      @else
                        <x-heroicon-s-check class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" />
                        Enable
                      @endif
                    </button>
                  </div>
                </x-navigation.dropdown>
              </div>
            </div>
          </div>
          <div>
            @if(!$monitor->uptime_check_enabled)
              <div class="bg-yellow-100 text-center  p-3">Uptime check is disabled</div>
              <div class="px-4 py-5 sm:p-0 opacity-20">
                <dl class="sm:divide-y sm:divide-gray-200">
                  <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Current Status</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                      N/A
                    </dd>
                  </div>
                  <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Last Checked</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">N/A</dd>
                  </div>
                </dl>
              </div>
            @else
              <div class="px-4 py-5 sm:p-0">
                <dl class="sm:divide-y sm:divide-gray-200">
                  <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Current Status</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                      @if($monitor->uptime_status === 'down')
                        Down
                      @elseif($monitor->uptime_status === 'not yet checked')
                        Pending
                      @else
                        Up
                      @endif
                    </dd>
                  </div>
                  <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Last Checked</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $monitor->uptime_last_check_date?->diffForHumans() }}</dd>
                  </div>
                  <div class="py-4 sm:py-5 sm:px-6">
                    <div class="flex w-full items-center justify-between">
                      <div>
                        <dt class="text-xs font-normal text-gray-500">Today</dt>
                        <dd class="mt-1 flex items-baseline justify-between text-2xl font-semibold text-sky-600 md:block lg:flex">
                          {{ $uptimeForToday }}%
                        </dd>
                      </div>
                      <div>
                        <dt class="text-xs font-normal text-gray-500">Last 7 Days</dt>
                        <dd class="mt-1 flex items-baseline justify-between text-2xl font-semibold text-sky-600 md:block lg:flex">
                          {{ $uptimeForLastSevenDays }}%
                        </dd>
                      </div>
                      <div>
                        <dt class="text-xs font-normal text-gray-500">Last 30 Days</dt>
                        <dd class="mt-1 flex items-baseline justify-between text-2xl font-semibold text-sky-600 md:block lg:flex">
                          {{ $uptimeForLastThirtyDays }}%
                        </dd>
                      </div>
                    </div>
                  </div>
                  @if($monitor->uptime_status === 'down')
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                      <dt class="text-sm font-medium text-gray-500">Failure Reason</dt>
                      <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $monitor->uptime_check_failure_reason }}</dd>
                    </div>
                  @endif
                </dl>
              </div>
            @endif
          </div>
        </div>

        <div class="bg-white shadow rounded-lg divide-y divide-gray-200">
          <div class="bg-gray-50 rounded-lg px-4 py-5 sm:px-6">
            <div class="-ml-4 -mt-4 flex justify-between items-center flex-wrap sm:flex-nowrap">
              <div class="ml-4 mt-4 flex items-center">
                @if($monitor->certificate_check_enabled)
                  <div @class([
                   'flex-shrink-0 w-3 h-3 m-1 mr-2 rounded-full ',
                   'bg-green-600' => $monitor->certificate_status === 'valid',
                   'bg-red-600' => $monitor->certificate_status === 'invalid',
                   'bg-yellow-600' => $monitor->certificate_status === 'not yet checked'
                    ]) aria-hidden="true"></div>
                @else
                  <div class="flex-shrink-0 w-3 h-3 m-1 mr-2 rounded-full bg-gray-50 border border-black" aria-hidden="true"></div>
                @endif
                <h3 class="text-lg leading-6 font-medium text-gray-900">SSL Certificate</h3>
              </div>
              <div class="ml-4 mt-4 flex-shrink-0">
                <!-- Certificate menu dropdown -->
                <x-navigation.dropdown>
                  <x-slot name="trigger">
                    <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500" aria-expanded="false" aria-haspopup="true">
                      <x-heroicon-s-ellipsis-vertical class="-ml-0.5 -mr-1 h-4 w-4" />
                      &nbsp;
                    </button>
                  </x-slot>

                  <div
                    class="origin-top-right z-50 absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                    role="menu" aria-orientation="vertical" aria-labelledby="certificate-menu-button" tabindex="-1">
                    <button wire:click="toggleCertificateCheck" class="w-full flex items-center group px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            role="menuitem" tabindex="-1" id="certificate-menu-item-0">
                      @if($monitor->certificate_check_enabled)
                        <x-heroicon-s-no-symbol class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" />
                        Disable
                      @else
                        <x-heroicon-s-check class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" />
                        Enable
                      @endif
                    </button>
                  </div>
                </x-navigation.dropdown>
              </div>
            </div>
          </div>
          <div>
            @if(!$monitor->certificate_check_enabled)
              <div class="bg-yellow-100 text-center  p-3">SSL Certificate check is disabled</div>
              <div class="px-4 py-5 sm:p-0 opacity-20">
                <dl class="sm:divide-y sm:divide-gray-200">
                  <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Current Status</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                      N/A
                    </dd>
                  </div>
                  <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Expiration Date</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">N/A</dd>
                  </div>
                </dl>
              </div>
            @else
              <div class="px-4 py-5 sm:p-0">
                <dl class="sm:divide-y sm:divide-gray-200">
                  <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Current Status</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                      @if($monitor->certificate_status === 'invalid')
                        Invalid
                      @elseif($monitor->certificate_status === 'not yet checked')
                        Pending
                      @else
                        Ok
                      @endif
                    </dd>
                  </div>
                  @if($monitor->certificate_status === 'valid')
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                      <dt class="text-sm font-medium text-gray-500">Expiration Date</dt>
                      <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $monitor->certificate_expiration_date->format("D, F j, Y, g:i a") }}</dd>
                    </div>
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                      <dt class="text-sm font-medium text-gray-500">Issued By</dt>
                      <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $monitor->certificate_issuer }}</dd>
                    </div>
                  @endif
                  @if($monitor->certificate_status === 'invalid')
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                      <dt class="text-sm font-medium text-gray-500">Failure Reason</dt>
                      <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $monitor->certificate_check_failure_reason }}</dd>
                    </div>
                  @endif
                </dl>
              </div>
            @endif
          </div>
        </div>

        <div class="bg-white shadow rounded-lg divide-y divide-gray-200">
          <div class="bg-gray-50 rounded-lg px-4 py-5 sm:px-6">
            <div class="-ml-4 -mt-4 flex justify-between items-center flex-wrap sm:flex-nowrap">
              <div class="ml-4 mt-4 flex items-center">
                @if($monitor->blacklist_check_enabled)
                  <div @class([
                   'flex-shrink-0 w-3 h-3 m-1 mr-2 rounded-full ',
                   'bg-green-600' => $monitor->blacklist_status === 'valid',
                   'bg-red-600' => $monitor->blacklist_status === 'invalid',
                   'bg-yellow-600' => $monitor->blacklist_status === 'not yet checked'
                    ]) aria-hidden="true"></div>
                @else
                  <div class="flex-shrink-0 w-3 h-3 m-1 mr-2 rounded-full bg-gray-50 border border-black" aria-hidden="true"></div>
                @endif
                <h3 class="text-lg leading-6 font-medium text-gray-900">Email Blacklist</h3>
              </div>
              <div class="ml-4 mt-4 flex-shrink-0">
                <!-- Blacklist menu dropdown -->
                <x-navigation.dropdown>
                  <x-slot name="trigger">
                    <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500" aria-expanded="false" aria-haspopup="true">
                      <x-heroicon-s-ellipsis-vertical class="-ml-0.5 -mr-1 h-4 w-4" />
                      &nbsp;
                    </button>
                  </x-slot>

                  <div
                    class="origin-top-right z-50 absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                    role="menu" aria-orientation="vertical" aria-labelledby="blacklist-menu-button" tabindex="-1">
                    <button wire:click="toggleBlacklistCheck" class="w-full flex items-center group px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            role="menuitem" tabindex="-1" id="blacklist-menu-item-0">
                      @if($monitor->blacklist_check_enabled)
                        <x-heroicon-s-no-symbol class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" />
                        Disable
                      @else
                        <x-heroicon-s-check class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" />
                        Enable
                      @endif
                    </button>
                  </div>
                </x-navigation.dropdown>
              </div>
            </div>
          </div>
          <div>
            @if(!$monitor->blacklist_check_enabled)
              <div class="bg-yellow-100 text-center  p-3">Blacklist check is disabled</div>
              <div class="px-4 py-5 sm:p-0 opacity-20">
                <dl class="sm:divide-y sm:divide-gray-200">
                  <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Current Status</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                      N/A
                    </dd>
                  </div>
                  <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">List</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">N/A</dd>
                  </div>
                </dl>
              </div>
            @else
              <div class="px-4 py-5 sm:p-0">
                <dl class="sm:divide-y sm:divide-gray-200">
                  <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Current Status</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                      @if($monitor->blacklist_status === 'invalid')
                        Invalid
                      @elseif($monitor->blacklist_status === 'not yet checked')
                        Pending
                      @else
                        Ok
                      @endif
                    </dd>
                  </div>
                  @if($monitor->blacklist_status === 'valid')
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                      <dt class="text-sm font-medium text-gray-500">List</dt>
                      <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">Not found on any blacklist</dd>
                    </div>
                  @endif
                  @if($monitor->blacklist_status === 'invalid')
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                      <dt class="text-sm font-medium text-gray-500">List</dt>
                      <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $monitor->blacklist_check_failure_reason }}</dd>
                    </div>
                  @endif
                </dl>
              </div>
            @endif
          </div>
        </div>
      </dl>
    </div>

    <!-- Accounts list (smallest breakpoint only) -->
    <div class="shadow sm:hidden mt-5">
      <ul role="list" class="mt-2 divide-y divide-gray-200 overflow-hidden shadow sm:hidden">
        @forelse($accounts as $account)
          <li>
            <a href="#"
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
          <li>
          <span class="block px-4 py-4 bg-white hover:bg-gray-50">
            No accounts found.
          </span>
          </li>
        @endforelse
      </ul>
    </div>

    <!-- Accounts table (small breakpoint and up) -->
    <div class="hidden sm:block mt-5">
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
                        <div x-data="{}">
                          @if($account->suspended)
                            <x-heroicon-s-no-symbol class="h-5 w-5 text-blue-600" x-tooltip.raw="Account Suspended" />
                          @elseif($account->is_disk_warning || $account->is_disk_critical || $account->is_disk_full)
                            <x-heroicon-s-exclamation-triangle class="h-5 w-5 text-red-500" x-tooltip.raw="Disk Warning" />
                          @else
                            <div class="flex-shrink-0 w-3 h-3 m-1 rounded-full bg-green-600" aria-hidden="true"></div>
                          @endif
                        </div>
                        <a href="{{ $account->domain_url }}" target="_blank" class="group inline-flex space-x-2 truncate text-sm">
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

    <!-- /End Content -->
  </div>

</div>
