<div>
  <!-- Page Header -->
  <div class="pb-3 border-b border-gray-200 sm:flex sm:items-center sm:justify-between">
    <h3 class="text-2xl leading-6 font-medium text-gray-900">
      Accounts
    </h3>
    <div class="mt-3 flex md:mt-0 gap-2">
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
            <flux:menu.radio value="duplicates">Duplicates</flux:menu.radio>
            <flux:menu.radio value="suspended">Suspended</flux:menu.radio>
            <flux:menu.radio value="noBackups">No Backups</flux:menu.radio>
          </flux:menu.radio.group>
        </flux:menu>
      </flux:dropdown>

      <flux:modal.trigger name="export-accounts-modal">
        <flux:button icon="arrow-down-tray">Export</flux:button>
      </flux:modal.trigger>
    </div>
  </div>
  <!-- / End Page Header -->

  <flux:card class="p-0 overflow-hidden bg-gray-50 mt-8">
    @if($this->filterBy !== 'none')
      <div class="px-6 py-4 flex justify-between items-center border-b border-zinc-800/10 dark:border-white/20 text-sm">
        <div class="flex items-center gap-3">
          Active filters
          @if($this->filterBy === 'duplicates')
            <flux:badge as="button" size="sm" rounded icon:trailing="x-mark" color="sky" wire:click="removeAllFilters">Duplicates</flux:badge>
          @endif
          @if($this->filterBy === 'suspended')
            <flux:badge as="button" size="sm" rounded icon:trailing="x-mark" color="sky" wire:click="removeAllFilters">Suspended</flux:badge>
          @endif
          @if($this->filterBy === 'noBackups')
            <flux:badge as="button" size="sm" rounded icon:trailing="x-mark" color="sky" wire:click="removeAllFilters">No Backups</flux:badge>
          @endif
        </div>
        <div>
          <flux:tooltip content="Remove all filters">
            <flux:button variant="subtle" size="sm" icon="x-mark" wire:click="removeAllFilters" />
          </flux:tooltip>
        </div>
      </div>
    @endif

    <flux:table :paginate="$this->accounts">
      <flux:table.columns>
        <flux:table.column class="px-6! bg-gray-50 font-medium text-gray-500! text-xs tracking-wide" sortable :sorted="$sortBy === 'domain'" :direction="$sortDirection" wire:click="sort('domain')">DOMAIN</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">WORDPRESS</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">BACKUPS</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">USED / LIMIT</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide" sortable :sorted="$sortBy === 'usage'" :direction="$sortDirection" wire:click="sort('usage')">USAGE</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide" sortable :sorted="$sortBy === 'newest'" :direction="$sortDirection" wire:click="sort('newest')">DATE ADDED</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">
          <span class="sr-only">Manage</span>
        </flux:table.column>
      </flux:table.columns>

      <flux:table.rows>
        @forelse ($this->accounts as $account)
          <flux:table.row :key="$account->id" @class([
                'bg-yellow-100' => $account->is_disk_warning,
                'bg-orange-100' => $account->is_disk_critical,
                'bg-red-100' => $account->is_disk_full,
                'bg-blue-200' => $account->suspended,
                'bg-gray-50' => $loop->even && !($account->is_disk_warning || $account->is_disk_critical || $account->is_disk_full || $account->suspended),
                'bg-white' => $loop->odd && !($account->is_disk_warning || $account->is_disk_critical || $account->is_disk_full || $account->suspended)
            ])>
            <flux:table.cell class="px-6! py-5!">
              <div class="flex-auto">
                <div>
                  <flux:link :href="route('accounts.show', $account->id)">{{ $account->domain }}</flux:link>
                  @if($account->suspended)
                    <flux:dropdown position="bottom" align="start">
                      <flux:badge as="button" size="sm" color="blue" inset="top bottom" icon:trailing="information-circle" class="ml-1">Suspended</flux:badge>

                      <flux:popover class="flex flex-col gap-3 rounded-xl shadow-xl">
                        <div>
                          This account was suspended on {{ $account->suspend_time->format('F d, Y \a\t g:ia') }}. It was suspended for "{{ $account->suspend_reason }}".
                        </div>
                      </flux:popover>
                    </flux:dropdown>
                  @endif
                </div>
                <div class="mt-1">
                  <flux:link variant="subtle" :href="route('servers.show', $account->server->id)">{{ $account->server->name }}</flux:link>
                </div>
              </div>
            </flux:table.cell>

            <flux:table.cell class="whitespace-nowrap">
              {{ $account->wordpress_version ?: '—' }}
            </flux:table.cell>

            <flux:table.cell>
              <flux:badge size="sm" :color="$account->backups_enabled ? 'green' : 'red'" inset="top bottom">{{ $account->backups_enabled ? 'Yes' : 'No'}}</flux:badge>
            </flux:table.cell>

            <flux:table.cell>
              {{ $account->disk_used }} / {{ $account->disk_limit }}
            </flux:table.cell>

            <flux:table.cell class="whitespace-nowrap">
              {{ $account->formatted_disk_usage !== 'Unknown' ? $account->formatted_disk_usage : '—' }}
            </flux:table.cell>

            <flux:table.cell class="whitespace-nowrap">{{ $account->created_at->format('M d, Y') }}</flux:table.cell>

            <flux:table.cell>
              <flux:tooltip content="View WHM Panel">
                <flux:button href="{{ $account->server->whm_url }}" size="sm" icon="arrow-top-right-on-square" target="_blank"></flux:button>
              </flux:tooltip>
            </flux:table.cell>
          </flux:table.row>
        @empty
          <flux:table.row>
            <flux:table.cell colspan="7" class="py-8 whitespace-nowrap font-semibold text-zinc-700">
              <div class="text-center">
                <div class="flex items-center justify-center">
                  <flux:icon.magnifying-glass class="size-12" />
                </div>
                <p class="text-lg mt-6">No accounts matched your search.</p>
              </div>
            </flux:table.cell>
          </flux:table.row>
        @endforelse
      </flux:table.rows>
    </flux:table>
  </flux:card>

  <!-- Export Accounts Modal -->
  <flux:modal name="export-accounts-modal" class="md:w-3xl">
    <form wire:submit="exportAccounts">
      <div class="space-y-6">
        <div class="sm:flex sm:items-start">
          <div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-green-100 sm:mx-0 sm:size-10 dark:bg-green-500/10">
            <flux:icon.arrow-down-tray class="text-green-500" />
          </div>
          <div class="ml-4 w-full">
            <flux:heading size="lg">Export Accounts</flux:heading>
            <flux:text>Select the columns you wish to include in this export.</flux:text>

            <div class="mt-4">
              <flux:checkbox.group wire:model="exportColumns">
                <flux:checkbox.all label="Select Columns" />
                <flux:separator class="mb-3" />

                <flux:checkbox label="Domain" value="domain" />
                <flux:checkbox label="Server" value="server" />
                <flux:checkbox label="Username" value="username" />
                <flux:checkbox label="IP Address" value="ip" />
                <flux:checkbox label="Backups" value="backups" />
                <flux:checkbox label="Suspended" value="suspended" />
                <flux:checkbox label="Suspended Reason" value="suspended_reason" />
                <flux:checkbox label="Suspended Date" value="suspended_time" />
                <flux:checkbox label="Setup Date" value="setup_date" />
                <flux:checkbox label="Disk Used" value="disk_used" />
                <flux:checkbox label="Disk Limit" value="disk_limit" />
                <flux:checkbox label="Disk Usage" value="disk_usage" />
                <flux:checkbox label="Plan" value="plan" />
                <flux:checkbox label="WordPress Version" value="wordpress_version" />
              </flux:checkbox.group>

              <flux:error name="exportColumns" />
            </div>
          </div>
        </div>
        <div class="flex gap-2">
          <flux:spacer />
          <flux:modal.close>
            <flux:button>Cancel</flux:button>
          </flux:modal.close>
          <flux:button type="submit" icon="check" variant="primary">Export</flux:button>
        </div>
      </div>
    </form>
  </flux:modal>
  <!-- /End Export Accounts Modal -->
</div>
