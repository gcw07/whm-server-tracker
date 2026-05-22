<div>
  <div class="pb-5">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
      <flux:link :href="route('reports.index')" variant="subtle">Reports</flux:link>
      <flux:icon.chevron-right class="size-4" />
      <span>WP Updates</span>
    </div>
    <h3 class="text-2xl leading-6 font-medium text-gray-900">
      WP Updates
    </h3>
    <p class="mt-1 text-sm text-gray-500">Plugin, theme and WordPress version update status for all monitored sites.</p>
  </div>

  <flux:card class="p-0 overflow-hidden bg-gray-50 mt-6">
    <flux:table :paginate="$this->monitors" pagination:scroll-to>
      <flux:table.columns>
        <flux:table.column class="px-6! bg-gray-50 font-medium text-gray-500! text-xs tracking-wide" sortable :sorted="$sortBy === 'url'" :direction="$sortDirection" wire:click="sort('url')">SITE</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">AGENT</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">WP VERSION</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">PLUGIN UPDATES</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">THEME UPDATES</flux:table.column>
      </flux:table.columns>

      <flux:table.rows>
        @forelse ($this->monitors as $monitor)
          @php $hasAgent = $monitor->wordpressCheck?->check_source === 'agent'; @endphp
          <flux:table.row :key="$monitor->id" @class([
                'bg-gray-50' => $loop->even,
                'bg-white' => $loop->odd,
                'opacity-40' => ! $hasAgent,
            ])>
            <flux:table.cell class="px-6! py-5!">
              <flux:link variant="subtle" :href="route('monitors.show', $monitor->id)">
                {{ preg_replace("(^https?://)", "", $monitor->url) }}
              </flux:link>
            </flux:table.cell>

            <flux:table.cell class="whitespace-nowrap">
              @if($hasAgent)
                <flux:badge size="sm" icon="check" color="green">Installed</flux:badge>
              @else
                <flux:badge size="sm" icon="x-mark" color="zinc">Not Installed</flux:badge>
              @endif
            </flux:table.cell>

            <flux:table.cell class="whitespace-nowrap text-sm text-gray-700">
              {{ $monitor->wordpressCheck?->wordpress_version ?? '—' }}
            </flux:table.cell>

            <flux:table.cell class="whitespace-nowrap">
              @if($hasAgent)
                @if(($monitor->wordpressCheck->plugin_updates_count ?? 0) > 0)
                  <flux:badge size="sm" icon="arrow-up-circle" color="yellow">{{ $monitor->wordpressCheck->plugin_updates_count }} available</flux:badge>
                @else
                  <flux:badge size="sm" icon="check" color="green">Up to date</flux:badge>
                @endif
              @else
                <span class="text-gray-400 text-sm">—</span>
              @endif
            </flux:table.cell>

            <flux:table.cell class="whitespace-nowrap">
              @if($hasAgent)
                @if(($monitor->wordpressCheck->theme_updates_count ?? 0) > 0)
                  <flux:badge size="sm" icon="arrow-up-circle" color="yellow">{{ $monitor->wordpressCheck->theme_updates_count }} available</flux:badge>
                @else
                  <flux:badge size="sm" icon="check" color="green">Up to date</flux:badge>
                @endif
              @else
                <span class="text-gray-400 text-sm">—</span>
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
                <p class="text-lg mt-6">No monitors found.</p>
              </div>
            </flux:table.cell>
          </flux:table.row>
        @endforelse
      </flux:table.rows>
    </flux:table>
  </flux:card>
</div>
