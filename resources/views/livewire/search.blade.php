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

    <div class="sm:flex sm:items-center">
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
                    <span class="truncate">
                      {{ $server->name }}
                    </span>
                    @if($server->missing_token)
                      <span class="inline-flex items-center px-2.5 py-0.5 mt-3 rounded-full text-sm font-medium bg-red-200 text-red-800 capitalize">
                        <x-heroicon-s-exclamation class="-ml-0.5 mr-1 h-4 w-4" />
                        no token
                      </span>
                    @else
                      <span><span class="text-gray-900 font-medium">{{ $server->accounts_count }}</span> accounts</span>
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
                              <x-heroicon-s-exclamation class="-ml-0.5 mr-1 h-4 w-4" />
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
                        <x-heroicon-s-external-link class="-ml-0.5 h-4 w-4" />
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


    <!-- /End Content -->
  </div>
</div>
