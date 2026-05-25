<div>
  <div class="pb-5">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
      <flux:link :href="route('reports.index')" variant="subtle">Reports</flux:link>
      <flux:icon.chevron-right class="size-4" />
      <span>Suspended Accounts</span>
    </div>
    <h3 class="text-2xl leading-6 font-medium text-gray-900">
      Suspended Accounts
    </h3>
    <p class="mt-1 text-sm text-gray-500">All suspended hosting accounts across all servers.</p>
  </div>

  <flux:card class="p-0 overflow-hidden bg-gray-50 mt-6">
    <flux:table :paginate="$this->accounts" pagination:scroll-to>
      <flux:table.columns>
        <flux:table.column class="px-6! bg-gray-50 font-medium text-gray-500! text-xs tracking-wide" sortable :sorted="$suspendedSortBy === 'domain'" :direction="$suspendedSortDirection" wire:click="sort('domain')">DOMAIN</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide" sortable :sorted="$suspendedSortBy === 'servers.name'" :direction="$suspendedSortDirection" wire:click="sort('servers.name')">SERVER</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide" sortable :sorted="$suspendedSortBy === 'suspend_time'" :direction="$suspendedSortDirection" wire:click="sort('suspend_time')">SUSPENDED</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">REASON</flux:table.column>
      </flux:table.columns>

      <flux:table.rows>
        @forelse ($this->accounts as $account)
          <flux:table.row :key="$account->id" @class(['bg-gray-50' => $loop->even, 'bg-white' => $loop->odd])>
            <flux:table.cell class="px-6! py-5!">
              <flux:link variant="subtle" :href="route('accounts.show', $account->id)">
                {{ $account->domain }}
              </flux:link>
            </flux:table.cell>

            <flux:table.cell class="whitespace-nowrap">
              <flux:link variant="subtle" :href="route('servers.show', $account->server_id)">
                {{ $account->server->name }}
              </flux:link>
            </flux:table.cell>

            <flux:table.cell class="whitespace-nowrap text-sm text-gray-700">
              @if ($account->suspend_time)
                <flux:tooltip :content="$account->suspend_time->format('F j, Y \a\t g:ia')">
                  <span class="cursor-default">{{ $account->suspend_time->diffForHumans() }}</span>
                </flux:tooltip>
              @else
                <span class="text-gray-400">Unknown</span>
              @endif
            </flux:table.cell>

            <flux:table.cell class="text-sm text-gray-700 max-w-xs">
              @if ($account->suspend_reason && $account->suspend_reason !== 'Unknown')
                {{ $account->suspend_reason }}
              @else
                <span class="text-gray-400">—</span>
              @endif
            </flux:table.cell>
          </flux:table.row>
        @empty
          <flux:table.row>
            <flux:table.cell colspan="4" class="py-8 font-semibold text-zinc-700">
              <div class="text-center">
                <div class="flex items-center justify-center">
                  <flux:icon.check-circle class="size-12 text-green-500" />
                </div>
                <p class="text-lg mt-6">No suspended accounts found.</p>
              </div>
            </flux:table.cell>
          </flux:table.row>
        @endforelse
      </flux:table.rows>
    </flux:table>
  </flux:card>
</div>
