<div>
  <div class="pb-5">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
      <flux:link :href="route('reports.index')" variant="subtle">Reports</flux:link>
      <flux:icon.chevron-right class="size-4" />
      <span>Disk Usage</span>
    </div>
    <h3 class="text-2xl leading-6 font-medium text-gray-900">
      Disk Usage
    </h3>
    <p class="mt-1 text-sm text-gray-500">Server and account disk usage across all tracked servers.</p>
  </div>

  {{-- Server Summary --}}
  @if ($this->servers->isNotEmpty())
    <div class="mt-6">
      <h4 class="text-base font-semibold text-gray-800 mb-3">Servers</h4>
      <flux:card class="p-0 overflow-hidden bg-gray-50">
        <flux:table>
          <flux:table.columns>
            <flux:table.column class="px-6! bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">SERVER</flux:table.column>
            <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">USED</flux:table.column>
            <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">TOTAL</flux:table.column>
            <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">USAGE</flux:table.column>
            <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">STATUS</flux:table.column>
          </flux:table.columns>

          <flux:table.rows>
            @foreach ($this->servers as $server)
              @php $pct = $server->settings->get('disk_percentage'); @endphp
              <flux:table.row :key="$server->id" @class(['bg-gray-50' => $loop->even, 'bg-white' => $loop->odd])>
                <flux:table.cell class="px-6! py-5!">
                  <flux:link variant="subtle" :href="route('servers.show', $server->id)">
                    {{ $server->name }}
                  </flux:link>
                </flux:table.cell>

                <flux:table.cell class="whitespace-nowrap text-sm text-gray-700">
                  {{ $server->formatted_disk_used }}
                </flux:table.cell>

                <flux:table.cell class="whitespace-nowrap text-sm text-gray-700">
                  {{ $server->formatted_disk_total }}
                </flux:table.cell>

                <flux:table.cell class="whitespace-nowrap">
                  <div class="flex items-center gap-2">
                    <div class="w-24 h-2 bg-gray-200 rounded-full overflow-hidden">
                      <div
                        class="h-full rounded-full @if($pct >= 90) bg-red-500 @elseif($pct >= 75) bg-amber-500 @else bg-violet-500 @endif"
                        style="width: {{ min($pct, 100) }}%"
                      ></div>
                    </div>
                    <span class="text-sm text-gray-700 tabular-nums">{{ $pct }}%</span>
                  </div>
                </flux:table.cell>

                <flux:table.cell class="whitespace-nowrap">
                  @if ($server->is_disk_full)
                    <flux:badge size="sm" icon="x-circle" color="red">Full</flux:badge>
                  @elseif ($server->is_disk_critical)
                    <flux:badge size="sm" icon="exclamation-triangle" color="red">Critical</flux:badge>
                  @elseif ($server->is_disk_warning)
                    <flux:badge size="sm" icon="clock" color="amber">Warning</flux:badge>
                  @else
                    <flux:badge size="sm" icon="check" color="green">OK</flux:badge>
                  @endif
                </flux:table.cell>
              </flux:table.row>
            @endforeach
          </flux:table.rows>
        </flux:table>
      </flux:card>
    </div>
  @endif

  {{-- Account Disk Usage --}}
  <div class="mt-10">
    <h4 class="text-base font-semibold text-gray-800 mb-3">Accounts</h4>
    <flux:card class="p-0 overflow-hidden bg-gray-50">
      <flux:table :paginate="$this->accounts" pagination:scroll-to>
        <flux:table.columns>
          <flux:table.column class="px-6! bg-gray-50 font-medium text-gray-500! text-xs tracking-wide" sortable :sorted="$diskSortBy === 'domain'" :direction="$diskSortDirection" wire:click="sort('domain')">DOMAIN</flux:table.column>
          <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">SERVER</flux:table.column>
          <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">USED / LIMIT</flux:table.column>
          <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide" sortable :sorted="$diskSortBy === 'disk_pct'" :direction="$diskSortDirection" wire:click="sort('disk_pct')">USAGE</flux:table.column>
          <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">STATUS</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
          @forelse ($this->accounts as $account)
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

              <flux:table.cell class="whitespace-nowrap text-sm text-gray-700">
                {{ $account->disk_used }} / {{ $account->disk_limit }}
              </flux:table.cell>

              <flux:table.cell class="whitespace-nowrap">
                @if ($account->formatted_disk_usage !== 'Unknown')
                  @php $pct = (float) $account->formatted_disk_usage; @endphp
                  <div class="flex items-center gap-2">
                    <div class="w-20 h-2 bg-gray-200 rounded-full overflow-hidden">
                      <div
                        class="h-full rounded-full @if($account->is_disk_full || $account->is_disk_critical) bg-red-500 @elseif($account->is_disk_warning) bg-amber-500 @else bg-violet-500 @endif"
                        style="width: {{ min($pct, 100) }}%"
                      ></div>
                    </div>
                    <span class="text-sm text-gray-700 tabular-nums">{{ $account->formatted_disk_usage }}</span>
                  </div>
                @else
                  <span class="text-gray-400 text-sm">—</span>
                @endif
              </flux:table.cell>

              <flux:table.cell class="whitespace-nowrap">
                @if ($account->is_disk_full)
                  <flux:badge size="sm" icon="x-circle" color="red">Full</flux:badge>
                @elseif ($account->is_disk_critical)
                  <flux:badge size="sm" icon="exclamation-triangle" color="red">Critical</flux:badge>
                @elseif ($account->is_disk_warning)
                  <flux:badge size="sm" icon="clock" color="amber">Warning</flux:badge>
                @else
                  <flux:badge size="sm" icon="check" color="green">OK</flux:badge>
                @endif
              </flux:table.cell>
            </flux:table.row>
          @empty
            <flux:table.row>
              <flux:table.cell colspan="5" class="py-8 whitespace-nowrap font-semibold text-zinc-700">
                <div class="text-center">
                  <div class="flex items-center justify-center">
                    <flux:icon.magnifying-glass class="size-12" />
                  </div>
                  <p class="text-lg mt-6">No accounts found.</p>
                </div>
              </flux:table.cell>
            </flux:table.row>
          @endforelse
        </flux:table.rows>
      </flux:table>
    </flux:card>
  </div>
</div>
