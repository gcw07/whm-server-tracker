<div>
  <div class="relative pb-5 sm:pb-0">
    <div class="md:flex md:items-center md:justify-between">
      <h3 class="text-2xl leading-6 font-medium text-gray-900">
        Monitors
      </h3>
      <div class="mt-3 flex md:mt-0 md:absolute md:top-3 md:right-0 gap-2">
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
              <flux:menu.radio value="disabled">Disabled Monitors</flux:menu.radio>
              <flux:menu.radio value="on_cloudflare">On Cloudflare</flux:menu.radio>
              <flux:menu.radio value="not_on_cloudflare">Not on Cloudflare</flux:menu.radio>
            </flux:menu.radio.group>
          </flux:menu>
        </flux:dropdown>
      </div>
    </div>
    <div class="mt-4">
      <flux:tab.group>
        <flux:tabs wire:model.live="monitorType">
          <flux:tab name="all" class="hover:border-gray-300">
            All
            <flux:badge size="sm" :color="$monitorType === 'all' ? 'cyan' : 'zinc'" rounded inset="top bottom" class="ml-2">
              {{ $this->issuesCount['all'] }}
            </flux:badge>
          </flux:tab>
          <flux:tab name="issues" class="hover:border-gray-300">
            Sites with issues
            <flux:badge size="sm" :color="$monitorType === 'issues' ? 'cyan' : 'zinc'" rounded inset="top bottom" class="ml-2">
              {{ $this->issuesCount['issues'] }}
            </flux:badge>
          </flux:tab>
        </flux:tabs>

        <!-- Must be added to avoid javascript error -->
        <flux:tab.panel name="all" class="hidden"></flux:tab.panel>
        <flux:tab.panel name="issues" class="hidden"></flux:tab.panel>

        <div class="pt-8">
          <flux:card class="p-0 overflow-hidden bg-gray-50">
            @if($this->filterBy !== 'none')
              <div class="px-6 py-4 flex justify-between items-center border-b border-zinc-800/10 dark:border-white/20 text-sm">
                <div class="flex items-center gap-3">
                  Active filters
                  @if($this->filterBy === 'disabled')
                    <flux:badge as="button" size="sm" rounded icon:trailing="x-mark" color="sky" wire:click="removeAllFilters">Disabled Monitors</flux:badge>
                  @endif
                  @if($this->filterBy === 'on_cloudflare')
                    <flux:badge as="button" size="sm" rounded icon:trailing="x-mark" color="sky" wire:click="removeAllFilters">On Cloudflare</flux:badge>
                  @endif
                  @if($this->filterBy === 'not_on_cloudflare')
                    <flux:badge as="button" size="sm" rounded icon:trailing="x-mark" color="sky" wire:click="removeAllFilters">Not on Cloudflare</flux:badge>
                  @endif
                </div>
                <div>
                  <flux:tooltip content="Remove all filters">
                    <flux:button variant="subtle" size="sm" icon="x-mark" wire:click="removeAllFilters" />
                  </flux:tooltip>
                </div>
              </div>
            @endif

            <flux:table :paginate="$this->monitors">
              <flux:table.columns>
                <flux:table.column class="px-6! bg-gray-50 font-medium text-gray-500! text-xs tracking-wide" sortable :sorted="$sortBy === 'url'" :direction="$sortDirection" wire:click="sort('url')">SITE</flux:table.column>
                <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">UPTIME</flux:table.column>
                <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">CERTIFICATE</flux:table.column>
                <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">BLACKLIST</flux:table.column>
                <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">CLOUDFLARE</flux:table.column>
                <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">
                  <span class="sr-only">Manage</span>
                </flux:table.column>
              </flux:table.columns>

              <flux:table.rows>
                @forelse ($this->monitors as $monitor)
                  <flux:table.row :key="$monitor->id" @class([
                        'bg-gray-50' => $loop->even,
                        'bg-white' => $loop->odd
                    ])>
                    <flux:table.cell class="px-6! py-5!">
                      <flux:link variant="subtle" :href="route('monitors.show', $monitor->id)">
                        {{ preg_replace("(^https?://)", "", $monitor->url ) }}
                      </flux:link>
                    </flux:table.cell>

                    <!-- Uptime Status -->
                    <flux:table.cell class="whitespace-nowrap">
                      @if(!$monitor->uptime_check_enabled)
                        <flux:badge size="sm" icon="no-symbol" color="zinc">Disabled</flux:badge>
                      @else
                        @if($monitor->uptime_status === 'down')
                          <flux:badge size="sm" icon="arrow-down" color="red">Down</flux:badge>
                        @elseif($monitor->uptime_status === 'not yet checked')
                          <flux:badge size="sm" icon="exclamation-triangle" color="yellow">Pending</flux:badge>
                        @else
                          <flux:badge size="sm" icon="check" color="green">Up</flux:badge>
                        @endif
                      @endif
                    </flux:table.cell>

                    <!-- Certificate Status -->
                    <flux:table.cell class="whitespace-nowrap">
                      @if(!$monitor->certificate_check_enabled)
                        <flux:badge size="sm" icon="no-symbol" color="zinc">Disabled</flux:badge>
                      @else
                        @if($monitor->certificate_status === 'invalid')
                          <flux:badge size="sm" icon="arrow-down" color="red">Invalid</flux:badge>
                        @elseif($monitor->certificate_status === 'not yet checked')
                          <flux:badge size="sm" icon="exclamation-triangle" color="yellow">Pending</flux:badge>
                        @else
                          <flux:badge size="sm" icon="check" color="green">Ok</flux:badge>
                        @endif
                      @endif
                    </flux:table.cell>

                    <!-- Blacklist Status -->
                    <flux:table.cell class="whitespace-nowrap">
                      @if(!$monitor->blacklist_check_enabled)
                        <flux:badge size="sm" icon="no-symbol" color="zinc">Disabled</flux:badge>
                      @else
                        @if($monitor->blacklist_status === 'invalid')
                          <flux:badge size="sm" icon="exclamation-triangle" color="red">Found</flux:badge>
                        @elseif($monitor->blacklist_status === 'not yet checked')
                          <flux:badge size="sm" icon="exclamation-triangle" color="yellow">Pending</flux:badge>
                        @else
                          <flux:badge size="sm" icon="check" color="green">Ok</flux:badge>
                        @endif
                      @endif
                    </flux:table.cell>

                    <!-- Cloudflare Status -->
                    <flux:table.cell class="whitespace-nowrap">
                      @if(!$monitor->domain_name_check_enabled)
                        <flux:badge size="sm" icon="no-symbol" color="zinc">Disabled</flux:badge>
                      @else
                        @if($monitor->is_on_cloudflare)
                          <flux:badge size="sm" icon="check" color="green">Yes</flux:badge>
                        @else
                          <flux:badge size="sm" icon="x-mark" color="red">No</flux:badge>
                        @endif
                      @endif
                    </flux:table.cell>

                    <flux:table.cell>
                      <flux:tooltip content="Visit Site">
                        <flux:button href="{{ $monitor->url }}" size="sm" icon="arrow-top-right-on-square" target="_blank"></flux:button>
                      </flux:tooltip>
                    </flux:table.cell>
                  </flux:table.row>
                @empty
                  <flux:table.row>
                    <flux:table.cell colspan="6" class="py-8 whitespace-nowrap font-semibold text-zinc-700">
                      <div class="text-center">
                        <div class="flex items-center justify-center">
                          <flux:icon.magnifying-glass class="size-12" />
                        </div>
                        <p class="text-lg mt-6">No monitors matched your search.</p>
                      </div>
                    </flux:table.cell>
                  </flux:table.row>
                @endforelse
              </flux:table.rows>
            </flux:table>
          </flux:card>
        </div>
      </flux:tab.group>
    </div>
  </div>
</div>
