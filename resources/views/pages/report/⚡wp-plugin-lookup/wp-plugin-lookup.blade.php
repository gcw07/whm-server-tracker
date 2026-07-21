<div>
  <div class="pb-5">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
      <flux:link :href="route('reports.index')" variant="subtle">Reports</flux:link>
      <flux:icon.chevron-right class="size-4" />
      <span>WP Plugin Lookup</span>
    </div>
    <h3 class="text-2xl leading-6 font-medium text-gray-900">
      WP Plugin Lookup
    </h3>
    <p class="mt-1 text-sm text-gray-500">Find which WordPress sites have (or are missing) a specific plugin.</p>
  </div>

  <div class="flex flex-wrap items-center gap-4 mb-6">
    <flux:select variant="listbox" searchable wire:model.live="selectedPlugin" placeholder="Select a plugin..." class="max-w-sm">
      @foreach ($this->pluginOptions as $plugin)
        <flux:select.option value="{{ $plugin }}">{{ $plugin }}</flux:select.option>
      @endforeach
    </flux:select>

    <div class="inline-flex rounded-lg shadow-xs border border-gray-200 overflow-hidden">
      <button
        wire:click="switchView('has')"
        @class([
          'flex items-center gap-1.5 px-4 py-2 text-sm font-medium transition-colors',
          'bg-accent text-white' => $view === 'has',
          'bg-white text-gray-600 hover:bg-gray-50' => $view !== 'has',
        ])
      >
        <flux:icon.check class="size-4" />
        Has Plugin
      </button>
      <button
        wire:click="switchView('missing')"
        @class([
          'flex items-center gap-1.5 px-4 py-2 text-sm font-medium transition-colors border-l border-gray-200',
          'bg-accent text-white' => $view === 'missing',
          'bg-white text-gray-600 hover:bg-gray-50' => $view !== 'missing',
        ])
      >
        <flux:icon.x-mark class="size-4" />
        Missing Plugin
      </button>
    </div>
  </div>

  <flux:card class="p-0 overflow-hidden bg-gray-50">
    <flux:table :paginate="$this->sites" pagination:scroll-to>
      <flux:table.columns>
        <flux:table.column class="px-6! bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">SITE</flux:table.column>
        @if ($view === 'has')
          <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">VERSION</flux:table.column>
          <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">PLUGIN STATUS</flux:table.column>
        @endif
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide w-16"></flux:table.column>
      </flux:table.columns>

      <flux:table.rows>
        @if (! $this->selectedPlugin)
          <flux:table.row>
            <flux:table.cell colspan="{{ $view === 'has' ? 4 : 2 }}" class="py-8 font-semibold text-zinc-700">
              <div class="text-center">
                <div class="flex items-center justify-center">
                  <flux:icon.magnifying-glass class="size-12 text-gray-300" />
                </div>
                <p class="text-lg mt-6">Select a plugin above to see results.</p>
              </div>
            </flux:table.cell>
          </flux:table.row>
        @else
          @forelse ($this->sites as $monitor)
            @php
              $plugin = $view === 'has' ? $monitor->wpPlugins->first() : null;
            @endphp
            <flux:table.row :key="$monitor->id" @class(['bg-gray-50' => $loop->even, 'bg-white' => $loop->odd])>
              <flux:table.cell class="px-6! py-5!">
                <a href="{{ route('monitors.show', $monitor->id) }}" class="font-medium text-gray-900 text-sm hover:underline">
                  {{ $monitor->domain_name }}
                </a>
              </flux:table.cell>

              @if ($view === 'has')
                <flux:table.cell class="whitespace-nowrap">
                  <span class="text-sm text-gray-600">{{ $plugin?->version }}</span>
                </flux:table.cell>
                <flux:table.cell class="whitespace-nowrap">
                  <flux:badge size="sm" :color="$plugin?->active ? 'green' : 'zinc'">
                    {{ $plugin?->active ? 'Active' : 'Deactivated' }}
                  </flux:badge>
                </flux:table.cell>
              @endif

              <flux:table.cell class="whitespace-nowrap">
                <flux:tooltip content="Open WP Admin">
                  <flux:button
                    href="{{ $monitor->url->withPath('/wp-admin') }}"
                    target="_blank"
                    size="sm"
                    icon="arrow-top-right-on-square"
                  ></flux:button>
                </flux:tooltip>
              </flux:table.cell>
            </flux:table.row>
          @empty
            <flux:table.row>
              <flux:table.cell colspan="{{ $view === 'has' ? 4 : 2 }}" class="py-8 font-semibold text-zinc-700">
                <div class="text-center">
                  <div class="flex items-center justify-center">
                    <flux:icon.check-circle class="size-12 text-green-500" />
                  </div>
                  <p class="text-lg mt-6">
                    @if ($view === 'has')
                      No sites have this plugin installed.
                    @else
                      No sites are missing this plugin.
                    @endif
                  </p>
                </div>
              </flux:table.cell>
            </flux:table.row>
          @endforelse
        @endif
      </flux:table.rows>
    </flux:table>
  </flux:card>
</div>
