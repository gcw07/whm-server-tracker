<div>
  <div class="pb-5">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
      <flux:link :href="route('reports.index')" variant="subtle">Reports</flux:link>
      <flux:icon.chevron-right class="size-4" />
      <span>Uptime Summary</span>
    </div>
    <h3 class="text-2xl leading-6 font-medium text-gray-900">
      Uptime Summary
    </h3>
    <p class="mt-1 text-sm text-gray-500">Uptime percentages and downtime totals for all monitored sites over the last 7 and 30 days.</p>
  </div>

  <flux:card class="p-0 overflow-hidden bg-gray-50 mt-6">
    <flux:table :paginate="$this->monitors" pagination:scroll-to>
      <flux:table.columns>
        <flux:table.column class="px-6! bg-gray-50 font-medium text-gray-500! text-xs tracking-wide" sortable :sorted="$uptimeSortBy === 'url'" :direction="$uptimeSortDirection" wire:click="sort('url')">SITE</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">SERVER</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">STATUS</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide" sortable :sorted="$uptimeSortBy === 'downtime_7d'" :direction="$uptimeSortDirection" wire:click="sort('downtime_7d')">LAST 7 DAYS</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide" sortable :sorted="$uptimeSortBy === 'downtime_30d'" :direction="$uptimeSortDirection" wire:click="sort('downtime_30d')">LAST 30 DAYS</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">OUTAGES (30D)</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">DOWNTIME (30D)</flux:table.column>
      </flux:table.columns>

      <flux:table.rows>
        @forelse ($this->monitors as $monitor)
          @php
            $uptime7d = $this->uptimePercentage((int) $monitor->downtime_7d, 7);
            $uptime30d = $this->uptimePercentage((int) $monitor->downtime_30d, 30);
            $downtimeHours = round($monitor->downtime_30d / 3600, 1);
          @endphp
          <flux:table.row :key="$monitor->id" @class(['bg-gray-50' => $loop->even, 'bg-white' => $loop->odd])>
            <flux:table.cell class="px-6! py-5!">
              <flux:link variant="subtle" :href="route('monitors.show', $monitor->id)">
                {{ preg_replace("(^https?://)", "", $monitor->url) }}
              </flux:link>
            </flux:table.cell>

            <flux:table.cell class="whitespace-nowrap text-sm text-gray-700">
              @if ($monitor->firstActiveAccount?->server)
                <flux:link variant="subtle" :href="route('servers.show', $monitor->firstActiveAccount->server)">
                  {{ $monitor->firstActiveAccount->server->name }}
                </flux:link>
              @else
                <span class="text-gray-400">—</span>
              @endif
            </flux:table.cell>

            <flux:table.cell class="whitespace-nowrap">
              @if ($monitor->uptime_status === 'up')
                <flux:badge size="sm" icon="check" color="green">Up</flux:badge>
              @elseif ($monitor->uptime_status === 'down')
                <flux:badge size="sm" icon="x-circle" color="red">Down</flux:badge>
              @else
                <flux:badge size="sm" color="zinc">Not checked</flux:badge>
              @endif
            </flux:table.cell>

            <flux:table.cell class="whitespace-nowrap">
              @if ($uptime7d >= 99.9)
                <flux:badge size="sm" color="green">{{ $uptime7d }}%</flux:badge>
              @elseif ($uptime7d >= 99)
                <flux:badge size="sm" color="amber">{{ $uptime7d }}%</flux:badge>
              @else
                <flux:badge size="sm" color="red">{{ $uptime7d }}%</flux:badge>
              @endif
            </flux:table.cell>

            <flux:table.cell class="whitespace-nowrap">
              @if ($uptime30d >= 99.9)
                <flux:badge size="sm" color="green">{{ $uptime30d }}%</flux:badge>
              @elseif ($uptime30d >= 99)
                <flux:badge size="sm" color="amber">{{ $uptime30d }}%</flux:badge>
              @else
                <flux:badge size="sm" color="red">{{ $uptime30d }}%</flux:badge>
              @endif
            </flux:table.cell>

            <flux:table.cell class="whitespace-nowrap text-sm text-gray-700">
              @if ($monitor->outage_count_30d > 0)
                {{ $monitor->outage_count_30d }}
              @else
                <span class="text-gray-400">0</span>
              @endif
            </flux:table.cell>

            <flux:table.cell class="whitespace-nowrap text-sm text-gray-700">
              @if ($monitor->downtime_30d > 0)
                {{ $downtimeHours }}h
              @else
                <span class="text-gray-400">—</span>
              @endif
            </flux:table.cell>
          </flux:table.row>
        @empty
          <flux:table.row>
            <flux:table.cell colspan="7" class="py-8 whitespace-nowrap font-semibold text-zinc-700">
              <div class="text-center">
                <div class="flex items-center justify-center">
                  <flux:icon.magnifying-glass class="size-12" />
                </div>
                <p class="text-lg mt-6">No uptime monitors found.</p>
              </div>
            </flux:table.cell>
          </flux:table.row>
        @endforelse
      </flux:table.rows>
    </flux:table>
  </flux:card>
</div>
