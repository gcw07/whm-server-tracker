<div>
  <div class="pb-5">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
      <flux:link :href="route('reports.index')" variant="subtle">Reports</flux:link>
      <flux:icon.chevron-right class="size-4" />
      <span>Cloudflare Traffic</span>
    </div>
    <h3 class="text-2xl leading-6 font-medium text-gray-900">
      Cloudflare Traffic
    </h3>
    <p class="mt-1 text-sm text-gray-500">Visitors, requests, and bandwidth totals for the last 30 days across all Cloudflare-enabled sites.</p>
  </div>

  <flux:card class="p-0 overflow-hidden bg-gray-50 mt-6">
    <flux:table :paginate="$this->monitors" pagination:scroll-to>
      <flux:table.columns>
        <flux:table.column class="px-6! bg-gray-50 font-medium text-gray-500! text-xs tracking-wide" sortable :sorted="$cfSortBy === 'monitors.url'" :direction="$cfSortDirection" wire:click="sort('monitors.url')">SITE</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide" sortable :sorted="$cfSortBy === 'visitors_30d'" :direction="$cfSortDirection" wire:click="sort('visitors_30d')">VISITORS (30D)</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide" sortable :sorted="$cfSortBy === 'requests_30d'" :direction="$cfSortDirection" wire:click="sort('requests_30d')">REQUESTS (30D)</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide" sortable :sorted="$cfSortBy === 'bandwidth_30d'" :direction="$cfSortDirection" wire:click="sort('bandwidth_30d')">BANDWIDTH (30D)</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">LAST SYNCED</flux:table.column>
      </flux:table.columns>

      <flux:table.rows>
        @forelse ($this->monitors as $monitor)
          <flux:table.row :key="$monitor->id" @class(['bg-gray-50' => $loop->even, 'bg-white' => $loop->odd])>
            <flux:table.cell class="px-6! py-5!">
              <flux:link variant="subtle" :href="route('monitors.show', $monitor->id)">
                {{ preg_replace("(^https?://)", "", $monitor->url) }}
              </flux:link>
            </flux:table.cell>

            <flux:table.cell class="whitespace-nowrap text-sm text-gray-700 tabular-nums">
              {{ number_format($monitor->visitors_30d) }}
            </flux:table.cell>

            <flux:table.cell class="whitespace-nowrap text-sm text-gray-700 tabular-nums">
              {{ number_format($monitor->requests_30d) }}
            </flux:table.cell>

            <flux:table.cell class="whitespace-nowrap text-sm text-gray-700 tabular-nums">
              {{ $this->formatBytes((int) $monitor->bandwidth_30d) }}
            </flux:table.cell>

            <flux:table.cell class="whitespace-nowrap text-sm text-gray-500">
              @if ($monitor->cf_last_synced_at)
                {{ \Illuminate\Support\Carbon::parse($monitor->cf_last_synced_at)->diffForHumans() }}
              @else
                <span class="text-gray-400">Never</span>
              @endif
            </flux:table.cell>
          </flux:table.row>
        @empty
          <flux:table.row>
            <flux:table.cell colspan="5" class="py-8 font-semibold text-zinc-700">
              <div class="text-center">
                <div class="flex items-center justify-center">
                  <flux:icon.magnifying-glass class="size-12" />
                </div>
                <p class="text-lg mt-6">No Cloudflare-enabled monitors found.</p>
              </div>
            </flux:table.cell>
          </flux:table.row>
        @endforelse
      </flux:table.rows>
    </flux:table>
  </flux:card>
</div>
