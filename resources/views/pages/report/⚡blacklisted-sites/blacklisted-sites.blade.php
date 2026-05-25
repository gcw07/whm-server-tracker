<div>
  <div class="pb-5">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
      <flux:link :href="route('reports.index')" variant="subtle">Reports</flux:link>
      <flux:icon.chevron-right class="size-4" />
      <span>Blacklisted Sites</span>
    </div>
    <h3 class="text-2xl leading-6 font-medium text-gray-900">
      Blacklisted Sites
    </h3>
    <p class="mt-1 text-sm text-gray-500">Email blacklist status for all monitored sites. Blacklisted sites appear first.</p>
  </div>

  <flux:card class="p-0 overflow-hidden bg-gray-50 mt-6">
    <flux:table :paginate="$this->monitors" pagination:scroll-to>
      <flux:table.columns>
        <flux:table.column class="px-6! bg-gray-50 font-medium text-gray-500! text-xs tracking-wide" sortable :sorted="$blacklistSortBy === 'url'" :direction="$blacklistSortDirection" wire:click="sort('url')">SITE</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide" sortable :sorted="$blacklistSortBy === 'status'" :direction="$blacklistSortDirection" wire:click="sort('status')">STATUS</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">LAST CHECKED</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">LISTED ON</flux:table.column>
      </flux:table.columns>

      <flux:table.rows>
        @forelse ($this->monitors as $monitor)
          @php
            $check = $monitor->blacklistCheck;
            $status = $check?->status;
            $isListed = $status?->value === 'invalid';
            $lastChecked = $check?->results->max('checked_at');
            $listedDrivers = $isListed
                ? $check->results->where('listed', true)
                : collect();
          @endphp
          <flux:table.row :key="$monitor->id" @class([
            'bg-gray-50' => $loop->even && ! $isListed,
            'bg-white' => $loop->odd && ! $isListed,
            'bg-red-50!' => $isListed,
          ])>
            <flux:table.cell class="px-6! py-5!">
              <flux:link variant="subtle" :href="route('monitors.show', $monitor->id)">
                {{ preg_replace("(^https?://)", "", $monitor->url) }}
              </flux:link>
            </flux:table.cell>

            <flux:table.cell class="whitespace-nowrap">
              @if ($isListed)
                <flux:badge size="sm" icon="exclamation-triangle" color="red">Listed</flux:badge>
              @elseif ($status?->value === 'not yet checked')
                <flux:badge size="sm" icon="clock" color="zinc">Pending</flux:badge>
              @else
                <flux:badge size="sm" icon="check" color="green">Clean</flux:badge>
              @endif
            </flux:table.cell>

            <flux:table.cell class="whitespace-nowrap text-sm text-gray-600">
              {{ $lastChecked?->diffForHumans() ?? 'Never' }}
            </flux:table.cell>

            <flux:table.cell>
              @if ($listedDrivers->isNotEmpty())
                <div class="flex flex-wrap gap-1">
                  @foreach ($listedDrivers as $result)
                    @if ($result->url)
                      <flux:badge as="a" href="{{ $result->url }}" target="_blank" size="sm" color="red" icon="exclamation-triangle">
                        {{ $result->driver }}
                      </flux:badge>
                    @else
                      <flux:badge size="sm" color="red" icon="exclamation-triangle">{{ $result->driver }}</flux:badge>
                    @endif
                  @endforeach
                </div>
              @else
                <span class="text-gray-400 text-sm">—</span>
              @endif
            </flux:table.cell>
          </flux:table.row>
        @empty
          <flux:table.row>
            <flux:table.cell colspan="4" class="py-8 font-semibold text-zinc-700">
              <div class="text-center">
                <div class="flex items-center justify-center">
                  <flux:icon.magnifying-glass class="size-12" />
                </div>
                <p class="text-lg mt-6">No monitors with blacklist checking enabled.</p>
              </div>
            </flux:table.cell>
          </flux:table.row>
        @endforelse
      </flux:table.rows>
    </flux:table>
  </flux:card>
</div>
