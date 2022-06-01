<div>
  <!-- Page Header -->
  <div class="pb-5 border-b border-gray-200 md:flex md:items-center md:justify-between">
    <div class="flex-1 min-w-0">
      <nav class="flex" aria-label="Breadcrumb">
        <ol role="list" class="flex items-center space-x-4">
          <li>
            <div class="flex">
              <a href="#" class="text-sm font-medium text-gray-500 hover:text-gray-700">Servers</a>
            </div>
          </li>
          <li>
            <div class="flex items-center">
              <x-heroicon-s-chevron-right class="flex-shrink-0 h-5 w-5 text-gray-400" />
              <a href="#" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">VPS</a>
            </div>
          </li>
        </ol>
      </nav>
      <h3 class="mt-2 text-2xl leading-6 font-medium text-gray-900">
        {{ $server->name }}
      </h3>
    </div>

    <div class="flex mt-3 md:mt-0 md:ml-4">
      <!--          <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">-->
      <!--            &lt;!&ndash; Heroicon name: solid/pencil &ndash;&gt;-->
      <!--            <svg class="-ml-0.5 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">-->
      <!--              <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />-->
      <!--            </svg>-->
      <!--            Edit-->
      <!--          </button>-->

      <a href="{{ $server->whm_url }}" target="_blank" type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
        <x-heroicon-s-external-link class="-ml-0.5 mr-2 h-4 w-4" />
        View
      </a>

      <button type="button" class="ml-2 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
        <x-heroicon-s-refresh class="-ml-0.5 mr-2 h-4 w-4" />
        Refresh
      </button>

      <button type="button" class="ml-2 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
        <x-heroicon-s-dots-vertical class="-ml-0.5 -mr-1 h-4 w-4" />
        &nbsp;
      </button>
    </div>
  </div>
  <!-- / End Page Header -->


  <div class="mt-6">
    <!-- Begin content -->

    <div class="hidden text-sm text-gray-500 sm:flex sm:justify-end">
      Last Updated: 35 minutes ago
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
              <x-heroicon-s-archive class="h-5 w-5 text-white" />
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
        <li>
          <a href="#" class="block px-4 py-4 bg-white hover:bg-gray-50">
                <span class="flex items-center space-x-4">
                  <span class="flex-1 flex space-x-2 truncate">
                    <span class="flex flex-col text-gray-500 text-sm truncate">
                      <span class="text-gray-900 font-medium truncate">centralokhose.com</span>
                      <span class="truncate">3137M / 8000M</span>
                      <span>39.2%</span>
                    </span>
                  </span>
                  <!-- Heroicon name: solid/chevron-right -->
                  <svg class="flex-shrink-0 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                  </svg>
                </span>
          </a>
        </li>

        <li>
          <a href="#" class="block px-4 py-4 bg-red-100 hover:bg-gray-50">
                <span class="flex items-center space-x-4">
                  <span class="flex-1 flex space-x-2 truncate">
                    <span class="flex flex-col text-gray-500 text-sm truncate">
                      <span class="flex items-center">
                        <span class="text-gray-900 font-medium truncate">furstclasscanine.com</span>
                        <svg class="ml-2 h-6 w-6 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                          </svg>
                      </span>
                      <span class="truncate">2878M / 5000M</span>
                      <span>57.6%</span>
                    </span>
                  </span>
                  <!-- Heroicon name: solid/chevron-right -->
                  <svg class="flex-shrink-0 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                  </svg>
                </span>
          </a>
        </li>

        <li>
          <a href="#" class="block px-4 py-4 bg-white hover:bg-gray-50">
                <span class="flex items-center space-x-4">
                  <span class="flex-1 flex space-x-2 truncate">
                    <span class="flex flex-col text-gray-500 text-sm truncate">
                      <span class="text-gray-900 font-medium truncate">minternational.com</span>
                      <span class="truncate">1048M / unlimited</span>
                      <span>&mdash;</span>
                    </span>
                  </span>
                  <!-- Heroicon name: solid/chevron-right -->
                  <svg class="flex-shrink-0 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                  </svg>
                </span>
          </a>
        </li>

        <li>
          <a href="#" class="block px-4 py-4 bg-white hover:bg-gray-50">
                <span class="flex items-center space-x-4">
                  <span class="flex-1 flex space-x-2 truncate">
                    <span class="flex flex-col text-gray-500 text-sm truncate">
                      <span class="text-gray-900 font-medium truncate">oklahomafoam.com</span>
                      <span class="truncate">520M / 40000M</span>
                      <span>1.3%</span>
                    </span>
                  </span>
                  <!-- Heroicon name: solid/chevron-right -->
                  <svg class="flex-shrink-0 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                  </svg>
                </span>
          </a>
        </li>

        <!-- More accounts... -->
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
                    Username
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
                @foreach($server->accounts as $account)
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
                            <x-heroicon-s-ban class="h-5 w-5 text-blue-600" />
                          @elseif($account->is_disk_warning || $account->is_disk_critical || $account->is_disk_full)
                            <x-heroicon-s-exclamation class="h-5 w-5 text-red-500" />
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
                    <td class="hidden px-6 py-4 whitespace-nowrap text-sm text-gray-900 lg:table-cell">
                      <div class="flex">
                        {{ $account->plan }}
                      </div>
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
                        <x-heroicon-s-external-link class="-ml-0.5 h-4 w-4" />
                      </a>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- /End Content -->
  </div>


</div>
