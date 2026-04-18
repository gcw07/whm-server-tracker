<div>
  <!-- Page Header -->
  <div class="relative pb-5 border-b border-gray-200 sm:pb-0 mt-2">
    <div class="md:flex md:items-center md:justify-between">
      <h3 class="text-2xl font-semibold text-gray-900 tracking-tight text-balance">
        Dashboard
      </h3>
    </div>
    <div class="mt-4">

    </div>
  </div>
  <!-- / End Page Header -->

  <div class="mt-6">

    @island(name: 'monitor-status')
      <div wire:poll.60s>
        @if($this->sitesWithIssues > 0)
          <div class="flex items-center gap-4 rounded-lg border border-red-200 border-l-4 border-l-red-500 bg-red-50 px-5 py-4">
            <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-red-100">
              <flux:icon.exclamation-triangle variant="solid" class="size-6 text-red-600" />
            </div>
            <div class="flex-1">
              <p class="text-sm font-semibold text-gray-900">{{ $this->sitesWithIssues }} {{ Str::plural('site', $this->sitesWithIssues) }} with issues</p>
              <p class="text-sm text-red-600/70">Monitor status requires attention.</p>
            </div>
            <a href="{{ route('monitors.index') }}" class="shrink-0 whitespace-nowrap text-sm font-medium text-red-600 hover:text-red-700">View &rarr;</a>
          </div>
        @endif
      </div>
    @endisland


    <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3">
      <div class="relative overflow-hidden bg-white rounded-lg border border-gray-950/10 border-t-2 border-t-sky-500 px-6 py-5 flex flex-col gap-4">
        <flux:icon.hard-drive class="absolute -bottom-3 -right-3 size-28 text-sky-500 opacity-[0.08] pointer-events-none" />
        <div class="flex items-center justify-between gap-2">
          <h2 class="text-xs font-medium text-gray-500 uppercase tracking-wide truncate">Server Disk Health</h2>
          @if($this->serversWithIssues === 0)
            <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700"><span class="size-1.5 rounded-full bg-green-500"></span>All healthy</span>
          @else
            <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-red-50 px-2.5 py-1 text-xs font-medium text-red-700"><span class="size-1.5 rounded-full bg-red-500"></span>{{ $this->serversWithIssues }} critical</span>
          @endif
        </div>
        <div class="flex items-end gap-6">
          <div>
            <p class="text-2xl font-semibold text-gray-900 tabular-nums">{{ $this->totalServers }}</p>
            <p class="text-xs text-gray-500 mt-1">Total</p>
          </div>
          <div>
            <p @class(['text-2xl font-semibold tabular-nums', 'text-red-600' => $this->serversWithIssues > 0, 'text-gray-400' => $this->serversWithIssues === 0])>{{ $this->serversWithIssues }}</p>
            <p class="text-xs text-gray-500 mt-1">Critical</p>
          </div>
        </div>
        <div class="text-xs"><flux:link :href="route('servers.index')" variant="subtle">View all servers &rarr;</flux:link></div>
      </div>

      <div class="relative overflow-hidden bg-white rounded-lg border border-gray-950/10 border-t-2 border-t-violet-500 px-6 py-5 flex flex-col gap-4">
        <flux:icon.globe class="absolute -bottom-3 -right-3 size-28 text-violet-500 opacity-[0.08] pointer-events-none" />
        <div class="flex items-center justify-between gap-2">
          <h2 class="text-xs font-medium text-gray-500 uppercase tracking-wide truncate">Account Health</h2>
          @if($this->suspendedAccounts === 0)
            <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700"><span class="size-1.5 rounded-full bg-green-500"></span>All active</span>
          @else
            <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700"><span class="size-1.5 rounded-full bg-amber-500"></span>{{ $this->suspendedAccounts }} suspended</span>
          @endif
        </div>
        <div class="flex items-end gap-6">
          <div>
            <p class="text-2xl font-semibold text-gray-900 tabular-nums">{{ $this->totalAccounts }}</p>
            <p class="text-xs text-gray-500 mt-1">Total</p>
          </div>
          <div>
            <p @class(['text-2xl font-semibold tabular-nums', 'text-amber-600' => $this->suspendedAccounts > 0, 'text-gray-400' => $this->suspendedAccounts === 0])>{{ $this->suspendedAccounts }}</p>
            <p class="text-xs text-gray-500 mt-1">Suspended</p>
          </div>
        </div>
        <div class="text-xs"><flux:link :href="route('accounts.index')" variant="subtle">View all accounts &rarr;</flux:link></div>
      </div>

      @island(name: 'monitor-status')
        <div wire:poll.60s class="relative overflow-hidden bg-white rounded-lg border border-gray-950/10 border-t-2 border-t-green-500 px-6 py-5 flex flex-col gap-4">
          <flux:icon.activity class="absolute -bottom-3 -right-3 size-28 text-green-500 opacity-[0.08] pointer-events-none" />
          <div class="flex items-center justify-between gap-2">
            <h2 class="text-xs font-medium text-gray-500 uppercase tracking-wide truncate">Monitor Status</h2>
            @if($this->monitorsDown === 0)
              <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700"><span class="size-1.5 rounded-full bg-green-500"></span>All online</span>
            @else
              <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-red-50 px-2.5 py-1 text-xs font-medium text-red-700"><span class="size-1.5 rounded-full bg-red-500"></span>{{ $this->monitorsDown }} down</span>
            @endif
          </div>
          <div class="flex items-end gap-6">
            <div>
              <p class="text-2xl font-semibold text-green-600 tabular-nums">{{ $this->totalMonitors - $this->monitorsDown }}</p>
              <p class="text-xs text-gray-500 mt-1">Online</p>
            </div>
            <div>
              <p @class(['text-2xl font-semibold tabular-nums', 'text-red-600' => $this->monitorsDown > 0, 'text-gray-400' => $this->monitorsDown === 0])>{{ $this->monitorsDown }}</p>
              <p class="text-xs text-gray-500 mt-1">Down</p>
            </div>
          </div>
          <div class="text-xs"><flux:link :href="route('monitors.index')" variant="subtle">View all monitors &rarr;</flux:link></div>
        </div>
      @endisland
    </div>

    <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-3">

      <!-- Latest Accounts -->
      <div class="md:col-span-2">
        <div class="sm:flex sm:items-center">
          <div class="sm:flex-auto">
            <h2 class="text-base font-semibold text-gray-800 text-balance">Latest Accounts</h2>
          </div>
        </div>
        <flux:card class="p-0 overflow-hidden bg-gray-50 mt-4">
          <flux:table>
            <flux:table.columns>
              <flux:table.column class="px-6! bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">DOMAIN</flux:table.column>
              <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">SERVER</flux:table.column>
              <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">DATE ADDED</flux:table.column>
              <flux:table.column class="bg-gray-50">
                <span class="sr-only">Actions</span>
              </flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
              @forelse ($this->recentAccounts as $account)
                <flux:table.row :key="$account->id" @class(['bg-gray-50' => $loop->even, 'bg-white' => $loop->odd])>
                  <flux:table.cell class="px-6! py-3.5!">
                    <flux:link variant="subtle" :href="route('accounts.show', $account->id)">{{ $account->domain }}</flux:link>
                  </flux:table.cell>
                  <flux:table.cell class="whitespace-nowrap">
                    <flux:link variant="subtle" :href="route('servers.show', $account->server->id)">{{ $account->server->name }}</flux:link>
                  </flux:table.cell>
                  <flux:table.cell class="whitespace-nowrap">{{ $account->created_at->diffForHumans() }}</flux:table.cell>
                  <flux:table.cell class="last:pe-3.5!">
                    <div class="flex justify-end gap-1">
                      @if($account->monitor_id)
                        <flux:tooltip content="View Monitor">
                          <flux:button :href="route('monitors.show', $account->monitor_id)" size="sm" icon="magnifying-glass" />
                        </flux:tooltip>
                      @endif
                      <flux:tooltip content="View WHM Panel">
                        <flux:button href="{{ $account->server->whm_url }}" size="sm" icon="arrow-top-right-on-square" target="_blank" />
                      </flux:tooltip>
                    </div>
                  </flux:table.cell>
                </flux:table.row>
              @empty
                <flux:table.row>
                  <flux:table.cell colspan="4" class="py-8 text-center font-semibold text-zinc-700">
                    <div class="flex items-center justify-center"><flux:icon.magnifying-glass class="size-12" /></div>
                    <p class="text-lg mt-6">No accounts found. Please add valid servers to start importing accounts.</p>
                  </flux:table.cell>
                </flux:table.row>
              @endforelse
            </flux:table.rows>
          </flux:table>
        </flux:card>
      </div>

      <!-- Server Disk Health -->
      <div>
        <div class="sm:flex sm:items-center">
          <div class="sm:flex-auto">
            <h2 class="text-base font-semibold text-gray-800 text-balance">Server Disk Health</h2>
          </div>
        </div>
        <flux:card class="p-0 overflow-hidden bg-gray-50 mt-4">
          <flux:table>
            <flux:table.columns>
              <flux:table.column class="px-6! bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">SERVER</flux:table.column>
              <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">DISK</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
              @forelse ($this->diskWarningServers as $server)
                <flux:table.row :key="$server->id" @class(['bg-gray-50' => $loop->even, 'bg-white' => $loop->odd])>
                  <flux:table.cell class="px-6! py-3.5!">
                    <flux:link variant="subtle" :href="route('servers.show', $server->id)">{{ $server->name }}</flux:link>
                  </flux:table.cell>
                  <flux:table.cell class="whitespace-nowrap">
                    <span @class([
                      'font-medium tabular-nums text-sm',
                      'text-red-600' => $server->is_disk_critical || $server->is_disk_full,
                      'text-amber-600' => $server->is_disk_warning,
                    ])>{{ $server->settings?->get('disk_percentage') }}%</span>
                  </flux:table.cell>
                </flux:table.row>
              @empty
                <flux:table.row>
                  <flux:table.cell colspan="2" class="py-6 text-center text-sm text-gray-500">
                    All servers healthy
                  </flux:table.cell>
                </flux:table.row>
              @endforelse
            </flux:table.rows>
          </flux:table>
        </flux:card>
      </div>

    </div>

  </div>
</div>
