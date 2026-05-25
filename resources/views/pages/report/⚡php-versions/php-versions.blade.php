<div>
  <div class="pb-5">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
      <flux:link :href="route('reports.index')" variant="subtle">Reports</flux:link>
      <flux:icon.chevron-right class="size-4" />
      <span>PHP Versions</span>
    </div>
    <h3 class="text-2xl leading-6 font-medium text-gray-900">
      PHP Versions
    </h3>
    <p class="mt-1 text-sm text-gray-500">PHP version breakdown across all hosting accounts.</p>
  </div>

  {{-- Version Summary --}}
  <div class="mt-6">
    <h4 class="text-base font-semibold text-gray-800 mb-3">Version Breakdown</h4>
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6">
      @forelse ($this->versionSummary as $version)
        <flux:card class="flex flex-col gap-2 p-4">
          <div class="flex items-center justify-between">
            <flux:badge size="sm" :color="$version['color']">{{ $version['status'] }}</flux:badge>
          </div>
          <p class="text-2xl font-bold text-gray-900">{{ $version['count'] }}</p>
          <p class="text-sm font-medium text-gray-700">{{ $version['name'] }}</p>
        </flux:card>
      @empty
        <p class="text-sm text-gray-500 col-span-full">No PHP version data found.</p>
      @endforelse
    </div>
  </div>

  {{-- Account Details --}}
  <div class="mt-10">
    <h4 class="text-base font-semibold text-gray-800 mb-3">Accounts</h4>
    <flux:card class="p-0 overflow-hidden bg-gray-50">
      <flux:table :paginate="$this->accounts" pagination:scroll-to>
        <flux:table.columns>
          <flux:table.column class="px-6! bg-gray-50 font-medium text-gray-500! text-xs tracking-wide" sortable :sorted="$sortBy === 'domain'" :direction="$sortDirection" wire:click="sort('domain')">DOMAIN</flux:table.column>
          <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">SERVER</flux:table.column>
          <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide" sortable :sorted="$sortBy === 'php_version'" :direction="$sortDirection" wire:click="sort('php_version')">PHP VERSION</flux:table.column>
          <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">SUPPORT STATUS</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
          @forelse ($this->accounts as $account)
            @php $phpInfo = $account->formatted_php_version; @endphp
            <flux:table.row :key="$account->id" @class(['bg-gray-50' => $loop->even, 'bg-white' => $loop->odd])>
              <flux:table.cell class="px-6! py-5!">
                <flux:link variant="subtle" :href="route('accounts.show', $account->id)">
                  {{ $account->domain }}
                </flux:link>
              </flux:table.cell>

              <flux:table.cell class="whitespace-nowrap text-sm text-gray-700">
                <flux:link variant="subtle" :href="route('servers.show', $account->server_id)">
                  {{ $account->server->name }}
                </flux:link>
              </flux:table.cell>

              <flux:table.cell class="whitespace-nowrap">
                @if (is_array($phpInfo))
                  <flux:badge size="sm" :color="$phpInfo['color']">{{ $phpInfo['name'] }}</flux:badge>
                @else
                  <span class="text-gray-400 text-sm">Unknown</span>
                @endif
              </flux:table.cell>

              <flux:table.cell class="whitespace-nowrap">
                @if (is_array($phpInfo))
                  @if ($phpInfo['status'] === 'active')
                    <flux:badge size="sm" icon="check" color="green">Active</flux:badge>
                  @elseif ($phpInfo['status'] === 'security')
                    <flux:badge size="sm" icon="shield-exclamation" color="amber">Security only</flux:badge>
                  @else
                    <flux:badge size="sm" icon="x-circle" color="red">End of life</flux:badge>
                  @endif
                @else
                  <span class="text-gray-400 text-sm">—</span>
                @endif
              </flux:table.cell>
            </flux:table.row>
          @empty
            <flux:table.row>
              <flux:table.cell colspan="4" class="py-8 whitespace-nowrap font-semibold text-zinc-700">
                <div class="text-center">
                  <div class="flex items-center justify-center">
                    <flux:icon.magnifying-glass class="size-12" />
                  </div>
                  <p class="text-lg mt-6">No accounts with PHP version data found.</p>
                </div>
              </flux:table.cell>
            </flux:table.row>
          @endforelse
        </flux:table.rows>
      </flux:table>
    </flux:card>
  </div>
</div>
