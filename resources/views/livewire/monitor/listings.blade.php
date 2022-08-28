<div wire:poll.60s>
  <!-- Page Header -->
  <div class="relative pb-5 border-b border-gray-200 sm:pb-0">
    <div class="md:flex md:items-center md:justify-between">
      <h3 class="text-2xl leading-6 font-medium text-gray-900">
        Monitors
      </h3>
      <div class="mt-3 flex md:mt-0 md:absolute md:top-3 md:right-0">
        <!-- Sort menu dropdown -->
        <x-navigation.dropdown>
          <x-slot name="trigger">
            <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500" aria-expanded="false" aria-haspopup="true">
              <x-heroicon-s-sort-descending class="-ml-0.5 mr-2 h-4 w-4" />
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
            <button wire:click.prevent="sortListingsBy('alpha_reversed')" class="w-full flex items-center group px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    role="menuitem" tabindex="-1" id="sort-menu-item-4">
              Reversed Alphabetically
            </button>
          </div>
        </x-navigation.dropdown>

        <!-- Filters menu dropdown -->
        <x-navigation.dropdown class="ml-2">
          <x-slot name="trigger">
            <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500" aria-expanded="false" aria-haspopup="true">
              <x-heroicon-s-filter class="-ml-0.5 mr-2 h-4 w-4" />
              Filters
            </button>
          </x-slot>

          <div
            class="origin-top-left z-50 absolute md:origin-top-right md:right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
            role="menu" aria-orientation="vertical" aria-labelledby="filters-menu-button" tabindex="-1">
            <button class="w-full flex items-center group px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    role="menuitem" tabindex="-1" id="filters-menu-item-0">
              Coming Soon
            </button>
          </div>
        </x-navigation.dropdown>
      </div>
    </div>
    <div class="mt-4">
      <!-- Dropdown menu on small screens -->
      <div class="sm:hidden">
        <label for="current-tab" class="sr-only">Select a tab</label>
        <select id="current-tab" name="current-tab" wire:model="hasIssues" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-sky-500 focus:border-sky-500 sm:text-sm rounded-md">
          <option value="false">All</option>
          <option value="true">Sites with issues</option>
        </select>
      </div>
      <!-- Tabs at small breakpoint and up -->
      <div class="hidden sm:block">
        <nav class="-mb-px flex space-x-8">
          <!-- Current: "border-sky-500 text-sky-600", Default: "border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" -->
          <button wire:click.prevent="filterIssues('false')"
            @class([
             'whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm',
             'border-sky-500 text-sky-600' => $hasIssues == false,
             'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' => $hasIssues === true,
            ])>
            All
            <span @class([
             'ml-3 py-0.5 px-2.5 rounded-full text-xs font-medium md:inline-block',
             'bg-gray-400 text-gray-100' => $hasIssues === true,
             'bg-sky-600 text-white' => $hasIssues === false,
            ])>
              {{ $issueTypeCounts['all'] }}
            </span>
          </button>
          <button wire:click.prevent="filterIssues('true')"
            @class([
             'whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm',
             'border-sky-500 text-sky-600' => $hasIssues === true,
             'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' => $hasIssues === false,
            ])>
            Sites with issues
            <span @class([
             'ml-3 py-0.5 px-2.5 rounded-full text-xs font-medium md:inline-block',
             'bg-gray-400 text-gray-100' => $hasIssues === false,
             'bg-sky-600 text-white' => $hasIssues === true,
            ])>
              {{ $issueTypeCounts['issues'] }}
            </span>
          </button>
        </nav>
      </div>
    </div>
  </div>
  <!-- / End Page Header -->

  <div class="mt-6">
    <!-- Begin content -->

    <!-- Server list (smallest breakpoint only) -->
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
                      {{ $monitor->url }}
                    </span>
                    <span><span class="font-medium">0</span> accounts</span>
                    <span>8%</span>
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
      {{ $monitors->links('livewire.pagination.index') }}
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
                    Site
                  </th>
                  <th scope="col" class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Uptime
                  </th>
                  <th scope="col" class="hidden px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider lg:table-cell">
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
                      @if($monitor->uptime_status === 'down')
                        <div class="inline-flex items-center rounded-md py-2 px-3 text-sm font-medium">
                          <x-heroicon-s-x-circle class="-ml-0.5 mr-2 h-4 w-4 text-red-600" />
                          <span class="text-gray-900 font-medium">Down</span>
                        </div>
                      @elseif($monitor->uptime_status === 'not yet checked')
                          <div class="inline-flex items-center rounded-md py-2 px-3 text-sm font-medium">
                            <x-heroicon-s-exclamation class="-ml-0.5 mr-2 h-4 w-4 text-yellow-600" />
                            <span class="text-gray-900 font-medium">Pending</span>
                          </div>
                      @else
                        <div class="inline-flex items-center rounded-md py-2 px-3 text-sm font-medium">
                          <x-heroicon-s-check-circle class="-ml-0.5 mr-2 h-4 w-4 text-green-600" />
                          <span class="text-gray-900 font-medium">Up</span>
                        </div>
                      @endif
                    </td>
                    <td class="px-6 py-4 text-center whitespace-nowrap text-sm text-gray-500">
                      @if($monitor->certificate_status === 'invalid')
                        <div class="inline-flex items-center rounded-md py-2 px-3 text-sm font-medium">
                          <x-heroicon-s-x-circle class="-ml-0.5 mr-2 h-4 w-4 text-red-600" />
                          <span class="text-gray-900 font-medium">Invalid</span>
                        </div>
                      @elseif($monitor->certificate_status === 'not yet checked')
                        <div class="inline-flex items-center rounded-md py-2 px-3 text-sm font-medium">
                          <x-heroicon-s-exclamation class="-ml-0.5 mr-2 h-4 w-4 text-yellow-600" />
                          <span class="text-gray-900 font-medium">Pending</span>
                        </div>
                      @else
                        <div class="inline-flex items-center rounded-md py-2 px-3 text-sm font-medium">
                          <x-heroicon-s-check-circle class="-ml-0.5 mr-2 h-4 w-4 text-green-600" />
                          <span class="text-gray-900 font-medium">Ok</span>
                        </div>
                      @endif
                    </td>
                    <td class="px-6 py-4 text-right whitespace-nowrap text-sm text-gray-500">
                      <a href="{{ $monitor->url }}" target="_blank" x-data="{}" x-tooltip.raw="Visit Site" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
                        <x-heroicon-s-external-link class="-ml-0.5 h-4 w-4" />
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
            {{ $monitors->links('livewire.pagination.index') }}
          </div>
        </div>
      </div>
    </div>

    <!-- /End Content -->
  </div>
</div>
