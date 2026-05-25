<div>
  <div class="pb-5">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
      <flux:link :href="route('reports.index')" variant="subtle">Reports</flux:link>
      <flux:icon.chevron-right class="size-4" />
      <span>Domain Expiry</span>
    </div>
    <h3 class="text-2xl leading-6 font-medium text-gray-900">
      Domain Expiry
    </h3>
    <p class="mt-1 text-sm text-gray-500">Domain registration expiry status for all monitored sites with domain checking enabled.</p>
  </div>

  <flux:card class="p-0 overflow-hidden bg-gray-50 mt-6">
    <flux:table :paginate="$this->domains" pagination:scroll-to>
      <flux:table.columns>
        <flux:table.column class="px-6! bg-gray-50 font-medium text-gray-500! text-xs tracking-wide" sortable :sorted="$domainSortBy === 'url'" :direction="$domainSortDirection" wire:click="sort('url')">SITE</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide" sortable :sorted="$domainSortBy === 'expiration_date'" :direction="$domainSortDirection" wire:click="sort('expiration_date')">EXPIRY DATE</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">STATUS</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">CLOUDFLARE</flux:table.column>
      </flux:table.columns>

      <flux:table.rows>
        @forelse ($this->domains as $monitor)
          @php
            $expiresAt = $monitor->domainCheck->expiration_date;
            $isExpired = $expiresAt->isPast();
            $daysRemaining = (int) now()->diffInDays($expiresAt);
          @endphp
          <flux:table.row :key="$monitor->id" @class(['bg-gray-50' => $loop->even, 'bg-white' => $loop->odd])>
            <flux:table.cell class="px-6! py-5!">
              <flux:link variant="subtle" :href="route('monitors.show', $monitor->id)">
                {{ preg_replace("(^https?://)", "", $monitor->url) }}
              </flux:link>
            </flux:table.cell>

            <flux:table.cell class="whitespace-nowrap text-sm text-gray-700">
              {{ $expiresAt->format('M j, Y') }}
            </flux:table.cell>

            <flux:table.cell class="whitespace-nowrap">
              @if ($isExpired)
                <flux:badge size="sm" icon="x-circle" color="red">Expired</flux:badge>
              @elseif ($daysRemaining <= 30)
                <flux:badge size="sm" icon="exclamation-triangle" color="red">{{ $daysRemaining }} days</flux:badge>
              @elseif ($daysRemaining <= 90)
                <flux:badge size="sm" icon="clock" color="amber">{{ $daysRemaining }} days</flux:badge>
              @else
                <flux:badge size="sm" icon="check" color="green">{{ $daysRemaining }} days</flux:badge>
              @endif
            </flux:table.cell>

            <flux:table.cell class="whitespace-nowrap">
              @if ($monitor->domainCheck->is_on_cloudflare)
                <flux:badge size="sm" color="orange" icon="bolt">Cloudflare</flux:badge>
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
                <p class="text-lg mt-6">No domain expiry data found.</p>
              </div>
            </flux:table.cell>
          </flux:table.row>
        @endforelse
      </flux:table.rows>
    </flux:table>
  </flux:card>
</div>
