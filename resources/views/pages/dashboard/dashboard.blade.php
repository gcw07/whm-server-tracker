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
              There are {{ $this->sitesWithIssues }} sites with issues.
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
              There are {{ $this->serversWithIssues }} servers running out of disk space.
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
          <div class="mt-2 flex flex-col">
            <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
              <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                  <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                      <tr>
                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Type</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">#</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                      <tr>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">Dedicated</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"> {{ $this->serverTypes->dedicated }} </td>
                      </tr>
                      <tr>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">Reseller</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">  {{ $this->serverTypes->reseller }} </td>
                      </tr>
                      <tr>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">VPS</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">  {{ $this->serverTypes->vps }} </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
      <div class="md:col-span-2">
        <div>
          <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
              <h1 class="text-lg font-semibold text-gray-700">Latest Accounts</h1>
            </div>
          </div>
          <div class="mt-2 flex flex-col">
            <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
              <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                  <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                      <tr>
                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Domain</th>
                        <th scope="col" class="px-2 py-3.5 text-left text-sm font-semibold text-gray-900">Server</th>
                        <th scope="col" class="px-2 py-3.5 text-left text-sm font-semibold text-gray-900">Date Added</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                      @forelse($this->recentAccounts as $account)
                        <tr>
                          <td class="whitespace-nowrap py-3 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                            <a href="{{ $account->domain_url }}" target="_blank" class="group inline-flex space-x-2 truncate text-sm">
                              <p class="text-gray-500 truncate group-hover:text-gray-900">
                                {{ $account->domain }}
                              </p>
                            </a>
                          </td>
                          <td class="whitespace-nowrap px-2 py-3 text-sm text-gray-500">
                            <a href="{{ route('servers.show', $account->server->id) }}" class="group inline-flex space-x-2 truncate text-sm">
                              <p class="text-gray-500 truncate group-hover:text-gray-900">
                                {{ $account->server->name }}
                              </p>
                            </a>
                          </td>
                          <td class="whitespace-nowrap px-2 py-3 text-sm text-gray-500"> {{ $account->created_at?->diffForHumans() }} </td>
                        </tr>
                      @empty
                        <tr>
                          <td colspan="3" class="whitespace-nowrap py-3 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                            <p class="text-center">No accounts found. Please add valid servers to start importing accounts.</p>
                          </td>
                        </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>


    <!-- /End Content -->
  </div>
</div>
