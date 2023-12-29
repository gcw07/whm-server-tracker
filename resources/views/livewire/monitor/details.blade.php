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
        {{ $domainUrl }}
      </h3>
    </div>

    <div class="flex mt-3 md:mt-0 md:ml-4">
      <a href="{{ $monitor->url }}" target="_blank" type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
        <x-heroicon-m-arrow-top-right-on-square class="-ml-0.5 mr-2 h-4 w-4" />
        View
      </a>

      <!-- Refresh menu dropdown -->
      <x-navigation.dropdown class="ml-2">
        <x-slot name="trigger">
          <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500" aria-expanded="false" aria-haspopup="true">
            <x-heroicon-s-arrow-path class="-ml-0.5 mr-2 h-4 w-4" />
            Refresh
            <x-heroicon-s-chevron-down class="ml-2 h-4 w-4" />
          </button>
        </x-slot>

        <div
          class="origin-top-right z-50 absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
          role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
          <button wire:click="refreshCertificateCheck" class="w-full flex items-center group px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  role="menuitem" tabindex="-1" id="details-menu-item-2">
            <x-heroicon-s-lock-closed class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" />
            SSL Certificate
          </button>
          <button wire:click="refreshBlacklistCheck" class="w-full flex items-center group px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  role="menuitem" tabindex="-1" id="details-menu-item-2">
            <x-heroicon-s-envelope class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" />
            Email Blacklist
          </button>
          <button wire:click="refreshDomainInfoCheck" class="w-full flex items-center group px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  role="menuitem" tabindex="-1" id="details-menu-item-2">
            <x-heroicon-s-identification class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" />
            Domain Info
          </button>
          <button wire:click="refreshLighthouseCheck" class="w-full flex items-center group px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  role="menuitem" tabindex="-1" id="details-menu-item-2">
            <x-heroicon-s-light-bulb class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" />
            Lighthouse Report
          </button>
        </div>
      </x-navigation.dropdown>

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
          <button wire:click="enableAllMonitors" class="w-full flex items-center group px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  role="menuitem" tabindex="-1" id="details-menu-item-2">
            <x-heroicon-s-bell class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" />
            Enable All Monitors
          </button>
          <button wire:click="disableAllMonitors" class="w-full flex items-center group px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  role="menuitem" tabindex="-1" id="details-menu-item-2">
            <x-heroicon-s-bell-slash class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" />
            Disable All Monitors
          </button>
        </div>
      </x-navigation.dropdown>
    </div>
  </div>
  <!-- / End Page Header -->


  <div class="mt-6">
    <!-- Begin content -->

    <div>
      <div class="bg-white shadow rounded-lg divide-y divide-gray-200">
        <div class="bg-gray-50 rounded-lg px-4 py-5 sm:px-6">
          <div class="-ml-4 -mt-4 flex justify-between items-center flex-wrap sm:flex-nowrap">
            <div class="ml-4 mt-4 flex items-center">
              <h3 class="text-lg leading-6 font-medium text-gray-900">Details</h3>
            </div>
          </div>
        </div>
        <div>
          <div class="px-4 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-200">
              @if($account->suspended)
                <div class="py-4 flex justify-center items-center text-base font-medium text-red-700 bg-red-50">
                  <x-heroicon-s-exclamation-triangle class="h-5 w-5 text-red-500 mr-2" />
                  This account is suspended
                </div>
              @endif
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Server</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                  <a href="{{ route('servers.show', $account->server->id) }}" class="group inline-flex space-x-2 truncate text-sm">
                    <p class="text-gray-500 truncate font-semibold group-hover:text-gray-900">
                      {{ $account->server->name }}
                    </p>
                  </a>
                  @if($accountsCount > 1)
                    <span class="ml-5 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-200 text-red-800 capitalize">
                      Found on multiple servers
                    </span>
                  @endif
                </dd>
              </div>
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Disk Usage</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                  {{ $account->formatted_disk_usage }}
                </dd>
              </div>
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">WordPress</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                  @if($account->wordpress_version)
                    {{ $account->wordpress_version }}
                  @else
                    WP not detected
                  @endif
                </dd>
              </div>
            </dl>
          </div>
        </div>
      </div>

      <div class="mt-5 bg-white shadow rounded-lg divide-y divide-gray-200">
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
              <h3 class="text-lg leading-6 font-medium text-gray-900">Uptime Checks</h3>
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

      <div class="mt-5 bg-white shadow rounded-lg divide-y divide-gray-200">
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

      <div class="mt-5 bg-white shadow rounded-lg divide-y divide-gray-200">
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
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                      {!! nl2br($monitor->blacklist_check_failure_reason) !!}
                    </dd>
                  </div>
                @endif
              </dl>
            </div>
          @endif
        </div>
      </div>

      <div class="mt-5 bg-white shadow rounded-lg divide-y divide-gray-200">
        <div class="bg-gray-50 rounded-lg px-4 py-5 sm:px-6">
          <div class="-ml-4 -mt-4 flex justify-between items-center flex-wrap sm:flex-nowrap">
            <div class="ml-4 mt-4 flex items-center">
              @if($monitor->domain_name_check_enabled)
                <div @class([
                   'flex-shrink-0 w-3 h-3 m-1 mr-2 rounded-full ',
                   'bg-green-600' => $monitor->domain_name_status === 'valid',
                   'bg-red-600' => $monitor->domain_name_status === 'invalid',
                   'bg-yellow-600' => $monitor->domain_name_status === 'not yet checked'
                    ]) aria-hidden="true"></div>
              @else
                <div class="flex-shrink-0 w-3 h-3 m-1 mr-2 rounded-full bg-gray-50 border border-black" aria-hidden="true"></div>
              @endif
              <h3 class="text-lg leading-6 font-medium text-gray-900">Domain Information</h3>
            </div>
            <div class="ml-4 mt-4 flex-shrink-0">
              <!-- Domain Name Expiration menu dropdown -->
              <x-navigation.dropdown>
                <x-slot name="trigger">
                  <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500" aria-expanded="false" aria-haspopup="true">
                    <x-heroicon-s-ellipsis-vertical class="-ml-0.5 -mr-1 h-4 w-4" />
                    &nbsp;
                  </button>
                </x-slot>

                <div
                  class="origin-top-right z-50 absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                  role="menu" aria-orientation="vertical" aria-labelledby="domain-name-menu-button" tabindex="-1">
                  <button wire:click="toggleDomainNameExpirationCheck" class="w-full flex items-center group px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                          role="menuitem" tabindex="-1" id="domain-name-menu-item-0">
                    @if($monitor->domain_name_check_enabled)
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
          @if(!$monitor->domain_name_check_enabled)
            <div class="bg-yellow-100 text-center  p-3">Domain Information checks are disabled</div>
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
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">On Cloudflare</dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    N/A
                  </dd>
                </div>
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">Nameservers</dt>
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
                    @if($monitor->domain_name_status === 'invalid')
                      Invalid
                    @elseif($monitor->domain_name_status === 'not yet checked')
                      Pending
                    @else
                      Valid
                    @endif
                  </dd>
                </div>
                @if($monitor->domain_name_status === 'valid')
                  <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Expiration Date</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $monitor->domain_name_expiration_date?->format("D, F j, Y, g:i a") }}</dd>
                  </div>
                @endif
                @if($monitor->domain_name_status === 'invalid')
                  <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Failed Reason</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $monitor->domain_name_check_failure_reason }}</dd>
                  </div>
                @endif
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">On Cloudflare</dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    @if($monitor->is_on_cloudflare)
                      Yes
                    @else
                      No
                    @endif
                  </dd>
                </div>
                @if($monitor->domain_name_status === 'valid')
                  <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Nameservers</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                      @if($monitor->nameservers)
                        @foreach($monitor->nameservers as $nameserver)
                          {{ $nameserver }}<br>
                        @endforeach
                      @else
                        Not fetched
                      @endif
                    </dd>
                  </div>
                @endif
              </dl>
            </div>
          @endif
        </div>
      </div>

      <div class="mt-5 bg-white shadow rounded-lg divide-y divide-gray-200">
        <div class="bg-gray-50 rounded-lg px-4 py-5 sm:px-6">
          <div class="-ml-4 -mt-4 flex justify-between items-center flex-wrap sm:flex-nowrap">
            <div class="ml-4 mt-4 flex items-center">
              @if($monitor->lighthouse_check_enabled)
                <div @class([
                   'flex-shrink-0 w-3 h-3 m-1 mr-2 rounded-full ',
                   'bg-green-600' => $monitor->lighthouse_status === 'valid',
                   'bg-red-600' => $monitor->lighthouse_status === 'invalid',
                   'bg-yellow-600' => $monitor->lighthouse_status === 'not yet checked'
                    ]) aria-hidden="true"></div>
              @else
                <div class="flex-shrink-0 w-3 h-3 m-1 mr-2 rounded-full bg-gray-50 border border-black" aria-hidden="true"></div>
              @endif
              <h3 class="text-lg leading-6 font-medium text-gray-900">Lighthouse Reports</h3>
            </div>
            <div class="ml-4 mt-4 flex-shrink-0">
              <!-- Lighthouse Reports menu dropdown -->
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
                  <button wire:click="toggleLighthouseCheck" class="w-full flex items-center group px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                          role="menuitem" tabindex="-1" id="lighthouse-menu-item-0">
                    @if($monitor->lighthouse_check_enabled)
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
          @if(!$monitor->lighthouse_check_enabled)
            <div class="bg-yellow-100 text-center  p-3">Lighthouse reports are disabled</div>
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
                    @if($monitor->lighthouse_status === 'invalid')
                      Invalid
                    @elseif($monitor->lighthouse_status === 'not yet checked')
                      Pending
                    @else
                      Ok
                    @endif
                  </dd>
                </div>
                @if($monitor->lighthouse_status === 'valid')
                  <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Last Checked</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $monitor->lighthouse_update_last_succeeded_at?->diffForHumans() }}</dd>
                  </div>
                  <div class="py-4 sm:py-5 sm:px-6">
                    <div class="flex w-full items-center justify-between">
                      <div>
                        <dt class="text-xs font-normal text-gray-500">Performance</dt>
                        <dd class="mt-1 flex items-baseline justify-between text-2xl font-semibold text-sky-600 md:block lg:flex">
                          {{ $lighthouseStats->performance_score }}
                        </dd>
                      </div>
                      <div>
                        <dt class="text-xs font-normal text-gray-500">Accessibility</dt>
                        <dd class="mt-1 flex items-baseline justify-between text-2xl font-semibold text-sky-600 md:block lg:flex">
                          {{ $lighthouseStats->accessibility_score }}
                        </dd>
                      </div>
                      <div>
                        <dt class="text-xs font-normal text-gray-500">Best Practices</dt>
                        <dd class="mt-1 flex items-baseline justify-between text-2xl font-semibold text-sky-600 md:block lg:flex">
                          {{ $lighthouseStats->best_practices_score }}
                        </dd>
                      </div>
                      <div>
                        <dt class="text-xs font-normal text-gray-500">SEO</dt>
                        <dd class="mt-1 flex items-baseline justify-between text-2xl font-semibold text-sky-600 md:block lg:flex">
                          {{ $lighthouseStats->seo_score }}
                        </dd>
                      </div>
                    </div>
                  </div>
                  <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <a href="{{ route('monitors.lighthouse', $monitor->id) }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">View Full Reports</a>
                  </div>
                @endif
                @if($monitor->lighthouse_status === 'invalid')
                  <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Last Checked</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $monitor->lighthouse_update_last_failed_at }}</dd>
                  </div>
                @endif
              </dl>
            </div>
          @endif
        </div>
      </div>
    </div>

    <!-- /End Content -->
  </div>

</div>
