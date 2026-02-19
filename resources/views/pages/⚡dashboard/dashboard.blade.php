<div>
  <!-- Page Header -->
  <div class="relative pb-5 border-b border-gray-200 sm:pb-0 mt-2">
    <div class="md:flex md:items-center md:justify-between">
      <h3 class="text-2xl leading-6 font-medium text-gray-900">
        Dashboard
      </h3>
    </div>
    <div class="mt-4">

    </div>
  </div>
  <!-- / End Page Header -->

  <div class="mt-6">
    <!-- Begin content -->

    @if($this->sitesWithIssues > 0)
      <div class="bg-red-50 border-l-4 border-red-400 p-4 drop-shadow">
        <div class="flex">
          <div class="shrink-0">
            <flux:icon.x-circle variant="solid" class="text-red-400" />
          </div>
          <div class="ml-3 flex-1 md:flex md:justify-between">
            <p class="text-sm text-red-700">
              There {{ $this->sitesWithIssues === 1 ? 'is' : 'are' }} {{ Str::plural('site', $this->sitesWithIssues, prependCount: true) }} with issues.
            </p>
            <p class="mt-3 text-sm md:mt-0 md:ml-6">
              <a href="{{ route('monitors.index') }}" class="whitespace-nowrap font-medium text-red-700 hover:text-red-600">View <span aria-hidden="true">&rarr;</span></a>
            </p>
          </div>
        </div>
      </div>
    @endif

    @if($this->serversWithIssues > 0)
      <div class="bg-red-50 border-l-4 border-red-400 p-4 drop-shadow">
        <div class="flex">
          <div class="shrink-0">
            <flux:icon.x-circle variant="solid" class="text-red-400" />
          </div>
          <div class="ml-3 flex-1 md:flex md:justify-between">
            <p class="text-sm text-red-700">
              There {{ $this->serversWithIssues === 1 ? 'is' : 'are' }} {{ Str::plural('server', $this->serversWithIssues, prependCount: true) }} running out of disk space.
            </p>
            <p class="mt-3 text-sm md:mt-0 md:ml-6">
              <a href="{{ route('servers.index') }}" class="whitespace-nowrap font-medium text-red-700 hover:text-red-600">View <span aria-hidden="true">&rarr;</span></a>
            </p>
          </div>
        </div>
      </div>
    @endif

    <div>
      <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <div class="relative bg-white pt-5 px-4 pb-3 sm:pt-6 sm:px-6 shadow rounded-lg overflow-hidden">
          <dt>
            <div class="absolute bg-sky-500 rounded-md p-3">
              <flux:icon.server class="text-white" />
            </div>
            <p class="ml-16 text-sm font-medium text-gray-500 truncate">Total Servers</p>
          </dt>
          <dd class="ml-16 pb-1 flex items-baseline sm:pb-2">
            <p class="text-2xl font-semibold text-gray-900">{{ $this->totalServers }}</p>
          </dd>
        </div>

        <div class="relative bg-white pt-5 px-4 pb-3 sm:pt-6 sm:px-6 shadow rounded-lg overflow-hidden">
          <dt>
            <div class="absolute bg-red-500 rounded-md p-3">
              <flux:icon.globe-alt class="text-white" />
            </div>
            <p class="ml-16 text-sm font-medium text-gray-500 truncate">Total Accounts</p>
          </dt>
          <dd class="ml-16 pb-1 flex items-baseline sm:pb-2">
            <p class="text-2xl font-semibold text-gray-900">{{ $this->totalAccounts }}</p>
          </dd>
        </div>

        <div class="relative bg-white pt-5 px-4 pb-3 sm:pt-6 sm:px-6 shadow rounded-lg overflow-hidden">
          <dt>
            <div class="absolute bg-green-500 rounded-md p-3">
              <flux:icon.sparkles class="text-white" />
            </div>
            <p class="ml-16 text-sm font-medium text-gray-500 truncate">Total Monitors</p>
          </dt>
          <dd class="ml-16 pb-1 flex items-baseline sm:pb-2">
            <p class="text-2xl font-semibold text-gray-900">{{ $this->totalMonitors }}</p>
          </dd>
        </div>
      </dl>
    </div>


    <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-3">

      <!--Server Types -->
      <div>
        <div>
          <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
              <h1 class="text-lg font-semibold text-gray-700">Server Types</h1>
            </div>
          </div>
          <flux:card class="p-0 overflow-hidden bg-gray-50 mt-4">
            <flux:table>
              <flux:table.columns>
                <flux:table.column class="px-6! bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">TYPE</flux:table.column>
                <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">#</flux:table.column>
              </flux:table.columns>

              <flux:table.rows>
                <flux:table.row class="bg-white">
                  <flux:table.cell class="px-6! py-3.5!">
                    Dedicated
                  </flux:table.cell>
                  <flux:table.cell class="whitespace-nowrap">
                    {{ $this->serverTypes->dedicated }}
                  </flux:table.cell>
                </flux:table.row>
                <flux:table.row class="bg-gray-50">
                  <flux:table.cell class="px-6! py-3.5!">
                    Reseller
                  </flux:table.cell>
                  <flux:table.cell class="whitespace-nowrap">
                    {{ $this->serverTypes->reseller }}
                  </flux:table.cell>
                </flux:table.row>
                <flux:table.row class="bg-white">
                  <flux:table.cell class="px-6! py-3.5!">
                    VPS
                  </flux:table.cell>
                  <flux:table.cell class="whitespace-nowrap">
                    {{ $this->serverTypes->vps }}
                  </flux:table.cell>
                </flux:table.row>
              </flux:table.rows>
            </flux:table>
          </flux:card>
        </div>

      </div>
      <div class="md:col-span-2">
        <div>
          <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
              <h1 class="text-lg font-semibold text-gray-700">Latest Accounts</h1>
            </div>
          </div>
          <flux:card class="p-0 overflow-hidden bg-gray-50 mt-4">
            <flux:table>
              <flux:table.columns>
                <flux:table.column class="px-6! bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">DOMAIN</flux:table.column>
                <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">SERVER</flux:table.column>
                <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">DATE ADDED</flux:table.column>
              </flux:table.columns>

              <flux:table.rows>
                @forelse ($this->recentAccounts as $account)
                  <flux:table.row :key="$account->id" @class([
                      'bg-gray-50' => $loop->even,
                      'bg-white' => $loop->odd
                  ])>
                    <flux:table.cell class="px-6! py-3.5!">
                      <flux:link variant="subtle" :href="route('accounts.show', $account->id)">{{ $account->domain }}</flux:link>
                    </flux:table.cell>

                    <flux:table.cell class="whitespace-nowrap">
                      <flux:link variant="subtle" :href="route('servers.show', $account->server->id)">{{ $account->server->name }}</flux:link>
                    </flux:table.cell>

                    <flux:table.cell class="whitespace-nowrap">{{ $account->created_at->diffForHumans() }}</flux:table.cell>
                  </flux:table.row>
                @empty
                  <flux:table.row>
                    <flux:table.cell colspan="3" class="py-8 whitespace-nowrap font-semibold text-zinc-700">
                      <div class="text-center">
                        <div class="flex items-center justify-center">
                          <flux:icon.magnifying-glass class="size-12" />
                        </div>
                        <p class="text-lg mt-6">No accounts found. Please add valid servers to start importing accounts.</p>
                      </div>
                    </flux:table.cell>
                  </flux:table.row>
                @endforelse
              </flux:table.rows>
            </flux:table>
          </flux:card>
        </div>

      </div>
    </div>


    <!-- /End Content -->
  </div>
</div>
