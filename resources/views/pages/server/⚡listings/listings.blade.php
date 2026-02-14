<div>
  <div class="relative pb-5 sm:pb-0">
    <div class="md:flex md:items-center md:justify-between">
      <h3 class="text-2xl leading-6 font-medium text-gray-900">
        Servers
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
              <flux:menu.radio value="no_backups">No Backups</flux:menu.radio>
              <flux:menu.radio value="outdated_php">Outdated PHP</flux:menu.radio>
            </flux:menu.radio.group>
          </flux:menu>
        </flux:dropdown>

        <flux:button :href="route('servers.create')" variant="primary" icon="plus">Create Server</flux:button>
      </div>
    </div>
    <div class="mt-4">
      <flux:tab.group>
        <flux:tabs wire:model.live="serverType">
          <flux:tab name="all" class="hover:border-gray-300">All</flux:tab>
          <flux:tab name="dedicated" class="hover:border-gray-300">Dedicated</flux:tab>
          <flux:tab name="reseller" class="hover:border-gray-300">Reseller</flux:tab>
          <flux:tab name="vps" class="hover:border-gray-300">VPS</flux:tab>
        </flux:tabs>

        <!-- Must be added to avoid javascript error -->
        <flux:tab.panel name="all" class="hidden"></flux:tab.panel>
        <flux:tab.panel name="dedicated" class="hidden"></flux:tab.panel>
        <flux:tab.panel name="reseller" class="hidden"></flux:tab.panel>
        <flux:tab.panel name="vps" class="hidden"></flux:tab.panel>

        <div class="pt-8">
          <flux:card class="p-0 overflow-hidden bg-gray-50">
            @if($this->filterBy !== 'none')
              <div class="px-6 py-4 flex justify-between items-center border-b border-zinc-800/10 dark:border-white/20 text-sm">
                <div class="flex items-center gap-3">
                  Active filters
                  @if($this->filterBy === 'no_backups')
                    <flux:badge as="button" size="sm" rounded icon:trailing="x-mark" color="sky" wire:click="removeAllFilters">No Backups</flux:badge>
                  @endif
                  @if($this->filterBy === 'outdated_php')
                    <flux:badge as="button" size="sm" rounded icon:trailing="x-mark" color="sky" wire:click="removeAllFilters">Outdated PHP</flux:badge>
                  @endif
                </div>
                <div>
                  <flux:tooltip content="Remove all filters">
                    <flux:button variant="subtle" size="sm" icon="x-mark" wire:click="removeAllFilters" />
                  </flux:tooltip>
                </div>
              </div>
            @endif

            <flux:table :paginate="$this->servers">
              <flux:table.columns>
                <flux:table.column class="px-6! bg-gray-50 font-medium text-gray-500! text-xs tracking-wide" sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">NAME</flux:table.column>
                <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide" sortable :sorted="$sortBy === 'accounts'" :direction="$sortDirection" wire:click="sort('accounts')">ACCOUNTS</flux:table.column>
                <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">BACKUPS</flux:table.column>
                <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">PHP</flux:table.column>
                <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide" sortable :sorted="$sortBy === 'usage'" :direction="$sortDirection" wire:click="sort('usage')">USAGE</flux:table.column>
                <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide" sortable :sorted="$sortBy === 'newest'" :direction="$sortDirection" wire:click="sort('newest')">SETUP DATE</flux:table.column>
                <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">
                  <span class="sr-only">Manage</span>
                </flux:table.column>
              </flux:table.columns>

              <flux:table.rows>
                @forelse ($this->servers as $server)
                  <flux:table.row :key="$server->id" @class([
                        'bg-yellow-100' => $server->is_disk_warning,
                        'bg-orange-100' => $server->is_disk_critical,
                        'bg-red-100' => $server->is_disk_full,
                        'bg-gray-50' => $loop->even && !($server->is_disk_warning || $server->is_disk_critical || $server->is_disk_full),
                        'bg-white' => $loop->odd && !($server->is_disk_warning || $server->is_disk_critical || $server->is_disk_full)
                    ])>
                    <flux:table.cell class="px-6! py-5!">
                      <flux:link variant="subtle" :href="route('servers.show', $server->id)">{{ $server->name }}</flux:link>
                      @if($server->missing_token)
                        <flux:badge size="sm" color="red" icon="exclamation-triangle" inset="top bottom">Missing token</flux:badge>
                      @endif
                      @if($server->is_disk_warning || $server->is_disk_critical || $server->is_disk_full)
                        <flux:dropdown position="bottom" align="start">
                          @php
                            if ($server->is_disk_warning) {
                              $warningColor = 'yellow';
                              $warningMessage = 'Disk warning';
                              $warningPercent = '80%';
                            } elseif ($server->is_disk_critical) {
                              $warningColor = 'orange';
                              $warningMessage = 'Disk critical';
                              $warningPercent = '90%';
                            } else {
                              $warningColor = 'red';
                              $warningMessage = 'Disk full';
                              $warningPercent = '100%';
                            }
                          @endphp
                          <flux:badge as="button" size="sm" :color="$warningColor" inset="top bottom" icon:trailing="exclamation-triangle" class="ml-1">{{ $warningMessage }}</flux:badge>

                          <flux:popover class="flex flex-col gap-3 rounded-xl shadow-xl">
                            <div>
                              This server has reached {{ $warningPercent }} of its disk total.
                            </div>
                          </flux:popover>
                        </flux:dropdown>
                      @endif
                    </flux:table.cell>

                    <flux:table.cell class="whitespace-nowrap">{{ $server->accounts_count }}</flux:table.cell>

                    <flux:table.cell>
                      <flux:badge size="sm" :color="$server->backups_enabled ? 'green' : 'red'" inset="top bottom">{{ $server->backups_enabled ? 'Yes' : 'No'}}</flux:badge>
                    </flux:table.cell>

                    <flux:table.cell>
                      @foreach($server->formatted_php_installed_versions as $version)
                        @php
                          if ($server->isPhpVersionEndOfLife($version)) {
                            $versionColor = 'red';
                          } elseif ($server->isPhpVersionSecurityOnly($version)) {
                            $versionColor = 'amber';
                          } else {
                            $versionColor = 'green';
                          }
                        @endphp
                        <flux:badge size="sm" :color="$versionColor" inset="top bottom">{{ $version }}</flux:badge>
                      @endforeach
                    </flux:table.cell>

                    <flux:table.cell class="whitespace-nowrap">{{ $server->settings->get('disk_percentage') }}%</flux:table.cell>

                    <flux:table.cell class="whitespace-nowrap">{{ $server->created_at->format('M d, Y') }}</flux:table.cell>

                    <flux:table.cell>
                      <flux:tooltip content="View WHM Panel">
                        <flux:button href="{{ $server->whm_url }}" size="sm" icon="arrow-top-right-on-square" target="_blank"></flux:button>
                      </flux:tooltip>
                    </flux:table.cell>
                  </flux:table.row>
                @empty
                  <flux:table.row>
                    <flux:table.cell colspan="8" class="py-8 whitespace-nowrap font-semibold text-zinc-700">
                      <div class="text-center">
                        <div class="flex items-center justify-center">
                          <flux:icon.magnifying-glass class="size-12" />
                        </div>
                        <p class="text-lg mt-6">No servers matched your search.</p>
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
