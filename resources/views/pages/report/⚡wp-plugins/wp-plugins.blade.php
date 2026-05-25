<div>
  <div class="pb-5">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
      <flux:link :href="route('reports.index')" variant="subtle">Reports</flux:link>
      <flux:icon.chevron-right class="size-4" />
      <span>WP Plugins</span>
    </div>
    <h3 class="text-2xl leading-6 font-medium text-gray-900">
      WP Plugins
    </h3>
    <p class="mt-1 text-sm text-gray-500">WordPress plugins with updates available, grouped by plugin name.</p>
  </div>

  <flux:card class="p-0 overflow-hidden bg-gray-50 mt-6">
    <flux:table :paginate="$this->pluginGroups" pagination:scroll-to>
      <flux:table.columns>
        <flux:table.column class="px-6! bg-gray-50 font-medium text-gray-500! text-xs tracking-wide" sortable :sorted="$pluginSortBy === 'name'" :direction="$pluginSortDirection" wire:click="sort('name')">PLUGIN</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide w-32" sortable :sorted="$pluginSortBy === 'site_count'" :direction="$pluginSortDirection" wire:click="sort('site_count')">SITES</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">AFFECTED SITES</flux:table.column>
      </flux:table.columns>

      <flux:table.rows>
        @forelse ($this->pluginGroups as $group)
          @php
            $sites = $this->monitorsByPlugin->get($group->name, collect());
            $visible = $sites->take(5);
            $overflow = $sites->count() - $visible->count();
          @endphp
          <flux:table.row :key="$group->name" @class(['bg-gray-50' => $loop->even, 'bg-white' => $loop->odd])>
            <flux:table.cell class="px-6! py-5!">
              <span class="font-medium text-gray-900 text-sm">{{ $group->name }}</span>
            </flux:table.cell>

            <flux:table.cell class="whitespace-nowrap">
              <flux:badge
                size="sm"
                :color="$group->site_count >= 10 ? 'red' : ($group->site_count >= 3 ? 'amber' : 'zinc')"
              >{{ $group->site_count }} {{ Str::plural('site', $group->site_count) }}</flux:badge>
            </flux:table.cell>

            <flux:table.cell>
              <div class="flex flex-wrap gap-1">
                @foreach ($visible as $plugin)
                  <flux:badge
                    as="a"
                    :href="route('monitors.show', $plugin->monitor_id)"
                    size="sm"
                    color="zinc"
                    inset="top bottom"
                    class="hover:bg-zinc-200!"
                  >
                    {{ preg_replace("(^https?://)", "", $plugin->monitor->url) }}
                  </flux:badge>
                @endforeach
                @if ($overflow > 0)
                  <span class="text-xs text-gray-400 self-center">and {{ $overflow }} more</span>
                @endif
              </div>
            </flux:table.cell>
          </flux:table.row>
        @empty
          <flux:table.row>
            <flux:table.cell colspan="3" class="py-8 font-semibold text-zinc-700">
              <div class="text-center">
                <div class="flex items-center justify-center">
                  <flux:icon.check-circle class="size-12 text-green-500" />
                </div>
                <p class="text-lg mt-6">All plugins are up to date.</p>
              </div>
            </flux:table.cell>
          </flux:table.row>
        @endforelse
      </flux:table.rows>
    </flux:table>
  </flux:card>
</div>
