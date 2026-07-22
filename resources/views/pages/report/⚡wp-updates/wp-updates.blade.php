<div>
  <div class="pb-5 flex items-center justify-between">
    <div>
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

    <flux:dropdown>
      <flux:button icon="adjustments-horizontal" icon:trailing="chevron-down">
        Filters
        @if($this->filterBy !== 'none')
          <flux:badge size="sm" class="ml-1">
            <span x-text="1" class="tabular-nums">&nbsp;</span>
          </flux:badge>
        @endif
      </flux:button>

      <flux:menu>
        <flux:menu.radio.group wire:model.live="filterBy">
          <flux:menu.radio value="agent_installed">Agent Installed</flux:menu.radio>
          <flux:menu.radio value="agent_not_installed">Agent Not Installed</flux:menu.radio>
        </flux:menu.radio.group>
      </flux:menu>
    </flux:dropdown>
  </div>

  <flux:card class="p-0 overflow-hidden bg-gray-50 mt-6">
    @if($this->filterBy !== 'none')
      <div class="px-6 py-4 flex justify-between items-center border-b border-zinc-800/10 dark:border-white/20 text-sm">
        <div class="flex items-center gap-3">
          Active filters
          @if($this->filterBy === 'agent_installed')
            <flux:badge as="button" size="sm" rounded icon:trailing="x-mark" color="sky" wire:click="removeAllFilters">Agent Installed</flux:badge>
          @endif
          @if($this->filterBy === 'agent_not_installed')
            <flux:badge as="button" size="sm" rounded icon:trailing="x-mark" color="sky" wire:click="removeAllFilters">Agent Not Installed</flux:badge>
          @endif
        </div>
        <div>
          <flux:tooltip content="Remove all filters">
            <flux:button variant="subtle" size="sm" icon="x-mark" wire:click="removeAllFilters" />
          </flux:tooltip>
        </div>
      </div>
    @endif

    <flux:table :paginate="$this->monitors" pagination:scroll-to>
      <flux:table.columns>
        <flux:table.column class="px-6! bg-gray-50 font-medium text-gray-500! text-xs tracking-wide" sortable :sorted="$sortBy === 'url'" :direction="$sortDirection" wire:click="sort('url')">SITE</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">AGENT</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide" sortable :sorted="$sortBy === 'wordpress_version'" :direction="$sortDirection" wire:click="sort('wordpress_version')">WP VERSION</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">PLUGIN UPDATES</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">THEME UPDATES</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">CORE UPDATE</flux:table.column>
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
              @if($monitor->all_accounts_suspended)
                <flux:dropdown position="bottom" align="start">
                  <flux:badge as="button" size="sm" color="blue" inset="top bottom" icon:trailing="information-circle" class="ml-1">Suspended</flux:badge>

                  <flux:popover class="flex flex-col gap-3 rounded-xl shadow-xl">
                    @if($monitor->accounts->count() === 1)
                      <div>
                        This account was suspended on {{ $monitor->accounts->first()->suspend_time->format('F d, Y \a\t g:ia') }}. It was suspended for "{{ $monitor->accounts->first()->suspend_reason }}".
                      </div>
                    @else
                      <ul class="flex flex-col gap-2">
                        @foreach($monitor->accounts as $account)
                          <li>{{ $account->server->name }}: suspended {{ $account->suspend_time->format('F d, Y') }} for "{{ $account->suspend_reason }}"</li>
                        @endforeach
                      </ul>
                    @endif
                  </flux:popover>
                </flux:dropdown>
              @endif
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

            <flux:table.cell class="whitespace-nowrap">
              @if($hasAgent)
                @if($monitor->wordpressCheck->core_update_version)
                  <flux:badge size="sm" icon="arrow-up-circle" color="yellow">{{ $monitor->wordpressCheck->core_update_version }} available</flux:badge>
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
            <flux:table.cell colspan="6" class="py-8 whitespace-nowrap font-semibold text-zinc-700">
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
