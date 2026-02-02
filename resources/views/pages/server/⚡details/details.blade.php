<div>
  <!-- Page Header -->
  <div class="pb-5 border-b border-gray-200 md:flex md:items-center md:justify-between">
    <div class="flex-1 min-w-0">
      <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('dashboard')">Home</flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('servers.index')">Servers</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>Details</flux:breadcrumbs.item>
      </flux:breadcrumbs>

      <h3 class="mt-2 text-2xl leading-6 font-medium text-gray-900">
        {{ $server->name }}
      </h3>
    </div>

    <div class="flex mt-3 md:mt-0 md:ml-4 gap-2">
      <flux:button :href="$server->whm_url" icon="arrow-top-right-on-square" target="_blank">View</flux:button>

      @if(!$server->missing_token)
        <flux:button wire:click="refresh" icon="arrow-path">Refresh</flux:button>
      @endif

      <flux:dropdown position="bottom" align="end">
        <flux:button icon="ellipsis-vertical" />

        <flux:menu>
          <flux:menu.item :href="route('servers.edit', $server)" icon="pencil-square">Edit</flux:menu.item>
          <flux:menu.item icon="key">Reset API Token</flux:menu.item>

          <flux:menu.separator />

          <flux:modal.trigger name="delete-server">
            <flux:menu.item variant="danger" icon="trash">Delete</flux:menu.item>
          </flux:modal.trigger>
        </flux:menu>
      </flux:dropdown>

      <!-- Details menu dropdown -->
{{--      <x-navigation.dropdown class="ml-2">--}}
{{--        <x-slot name="trigger">--}}
{{--          <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500" aria-expanded="false" aria-haspopup="true">--}}
{{--            <x-heroicon-s-ellipsis-vertical class="-ml-0.5 -mr-1 h-4 w-4" />--}}
{{--            &nbsp;--}}
{{--          </button>--}}
{{--        </x-slot>--}}
{{--        --}}
{{--        <div--}}
{{--          class="origin-top-right z-50 absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"--}}
{{--          role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">--}}
{{--          <a href="{{ route('servers.edit', $server) }}" class="flex items-center group px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"--}}
{{--             role="menuitem" tabindex="-1" id="details-menu-item-0">--}}
{{--            <x-heroicon-s-pencil-square class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" />--}}
{{--            Edit--}}
{{--          </a>--}}
{{--          @if(!$server->missing_token)--}}
{{--            <button wire:click="$dispatch('openModal', { component: 'server.reset-token', arguments: { server: {{ $server->id }} }})" class="w-full flex items-center group px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"--}}
{{--                    role="menuitem" tabindex="-1" id="details-menu-item-1">--}}
{{--              <x-heroicon-s-key class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" />--}}
{{--              Reset API Token--}}
{{--            </button>--}}
{{--          @endif--}}
{{--          <button wire:click="$dispatch('openModal', { component: 'server.delete', arguments: { server: {{ $server->id }} }})" class="w-full flex items-center group px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"--}}
{{--                  role="menuitem" tabindex="-1" id="details-menu-item-2">--}}
{{--            <x-heroicon-s-trash class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" />--}}
{{--            Delete--}}
{{--          </button>--}}
{{--        </div>--}}
{{--      </x-navigation.dropdown>--}}
    </div>
  </div>
  <!-- / End Page Header -->


  <div class="mt-6">
    <!-- Begin content -->

    @if($server->missing_token)
      <button wire:click="$dispatch('openModal', { component: 'server.new-token', arguments: { server: {{ $server->id }} }})" type="button" class="relative block w-full border-2 border-gray-300 border-dashed rounded-lg p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
{{--        <x-heroicon-o-key class="mx-auto h-12 w-12 text-gray-400" />--}}
        <span class="mt-2 block text-sm font-medium text-gray-900"> Add an API token to get started </span>
      </button>

    @else
      <div class="hidden text-sm text-gray-500 sm:flex sm:justify-end">
        Last Updated:
        @if($server->server_update_last_succeeded_at)
          {{ $server->server_update_last_succeeded_at->diffForHumans() }}
        @else
          Never
        @endif
      </div>
      <div class="hidden sm:block">
        <dl class="mt-5 grid grid-cols-1 rounded-lg bg-white overflow-hidden shadow lg:grid-cols-3">
          <div class="lg:border-r lg:border-gray-200">
            <dt class="bg-gray-50 border-b border-gray-200 text-lg p-5 font-normal text-gray-900 flex items-center">
              <div class="bg-sky-500 rounded-md p-1 mr-2">
                <flux:icon.information-circle variant="solid" class="text-white" />
              </div>
              Details
            </dt>
            <dl class="sm:divide-y sm:divide-gray-200 sm:px-5 sm:pt-4 sm:pb-1">
              <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="text-sm font-medium text-gray-400">
                  Accounts
                </dt>
                <dd class="mt-1 text-sm font-semibold text-gray-600 sm:mt-0 sm:col-span-2">
                  {{ $server->accounts_count }}
                </dd>
              </div>
              <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="text-sm font-medium text-gray-400">
                  Server type
                </dt>
                <dd class="mt-1 text-sm font-semibold text-gray-600 sm:mt-0 sm:col-span-2">
                  {{ $server->formatted_server_type }}
                  <span class="text-gray-400 font-medium">&mdash;</span>
                  <span>WHM {{ $server->formatted_whm_version }}</span>
                </dd>
              </div>
              <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="text-sm font-medium text-gray-400">
                  PHP versions
                </dt>
                <dd class="mt-1 text-sm font-semibold text-gray-600 sm:mt-0 sm:col-span-2">
                  @foreach($server->formatted_php_installed_versions as $version)
                    {{ $version }}@if (!$loop->last),@endif
                  @endforeach
                  <span class="text-gray-400 font-medium">&mdash;</span>
                  <span>System {{ $server->formatted_php_system_version }}</span>
                </dd>
              </div>
              <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="text-sm font-medium text-gray-400">
                  Server URL
                </dt>
                <dd class="mt-1 text-sm font-semibold text-gray-600 sm:mt-0 sm:col-span-2">
                  {{ $server->address }}:{{ $server->port }}
                </dd>
              </div>
            </dl>
          </div>

          <div class="lg:border-r lg:border-gray-200">
            <dt class="bg-gray-50 border-b border-gray-200 text-lg p-5 font-normal text-gray-900 flex items-center">
              <div class="bg-sky-500 rounded-md p-1 mr-2">
                <flux:icon.server variant="solid" class="text-white" />
              </div>
              Disk
            </dt>
            <dl class="sm:divide-y sm:divide-gray-200 sm:px-5 sm:pt-4 sm:pb-1">
              <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="text-sm font-medium text-gray-400">
                  Usage
                </dt>
                <dd class="mt-1 text-sm font-semibold text-gray-600 sm:mt-0 sm:col-span-2">
                  {{ $server->settings->get('disk_percentage') }}%
                </dd>
              </div>
              <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="text-sm font-medium text-gray-400">
                  Used
                </dt>
                <dd class="mt-1 text-sm font-semibold text-gray-600 sm:mt-0 sm:col-span-2">
                  {{ $server->formatted_disk_used }}
                </dd>
              </div>
              <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="text-sm font-medium text-gray-400">
                  Available
                </dt>
                <dd class="mt-1 text-sm font-semibold text-gray-600 sm:mt-0 sm:col-span-2">
                  {{ $server->formatted_disk_available }}
                </dd>
              </div>
              <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="text-sm font-medium text-gray-400">
                  Total
                </dt>
                <dd class="mt-1 text-sm font-semibold text-gray-600 sm:mt-0 sm:col-span-2">
                  {{ $server->formatted_disk_total }}
                </dd>
              </div>
            </dl>
          </div>

          <div>
            <dt class="bg-gray-50 border-b border-gray-200 text-lg p-5 font-normal text-gray-900 flex items-center">
              <div class="bg-sky-500 rounded-md p-1 mr-2">
                <flux:icon.archive-box variant="solid" class="text-white" />
              </div>
              Backups
            </dt>
            <dl class="sm:divide-y sm:divide-gray-200 sm:px-5 sm:pt-4 sm:pb-1">
              <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="text-sm font-medium text-gray-400">
                  Enabled
                </dt>
                <dd class="mt-1 text-sm font-semibold text-gray-600 sm:mt-0 sm:col-span-2">
                  @if($server->backups_enabled)
                    Yes
                  @else
                    No
                  @endif
                </dd>
              </div>
              <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="text-sm font-medium text-gray-400">
                  Daily
                </dt>
                <dd class="mt-1 text-sm font-semibold text-gray-600 sm:mt-0 sm:col-span-2">
                  @if($server->settings->get('backup_daily_enabled'))
                    {{ $server->settings->get('backup_daily_retention') }} / {{ $server->formatted_backup_daily_days }}
                  @else
                    Disabled
                  @endif
                </dd>
              </div>
              <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="text-sm font-medium text-gray-400">
                  Weekly
                </dt>
                <dd class="mt-1 text-sm font-semibold text-gray-600 sm:mt-0 sm:col-span-2">
                  @if($server->settings->get('backup_weekly_enabled'))
                    {{ $server->settings->get('backup_weekly_retention') }} / {{ $server->formatted_backup_weekly_day }}
                  @else
                    Disabled
                  @endif
                </dd>
              </div>
              <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="text-sm font-medium text-gray-400">
                  Monthly
                </dt>
                <dd class="mt-1 text-sm font-semibold text-gray-600 sm:mt-0 sm:col-span-2">
                  @if($server->settings->get('backup_monthly_enabled'))
                    {{ $server->settings->get('backup_monthly_retention') }} / {{ $server->formatted_backup_monthly_days }}
                  @else
                    Disabled
                  @endif
                </dd>
              </div>
            </dl>
          </div>
          <div class="col-span-1 lg:col-span-3 text-sm font-normal text-gray-600 p-4 flex sm:border-t sm:border-gray-200">
            <span class="font-semibold mr-4">Notes</span>
            <p>{{ $server->notes }}</p>
          </div>
        </dl>
      </div>

      <flux:card class="p-0 overflow-hidden bg-gray-50 mt-8">
        <flux:table>
          <flux:table.columns>
            <flux:table.column class="px-6! bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">DOMAIN</flux:table.column>
            <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">WORDPRESS</flux:table.column>
            <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">BACKUPS</flux:table.column>
            <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">PLAN</flux:table.column>
            <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">USED / LIMIT</flux:table.column>
            <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">USAGE</flux:table.column>
            <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">DATE ADDED</flux:table.column>
            <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">
              <span class="sr-only">Manage</span>
            </flux:table.column>
          </flux:table.columns>

          <flux:table.rows>
            @forelse ($this->server->accounts as $account)
              <flux:table.row :key="$account->id" @class([
                        'bg-yellow-100' => $account->is_disk_warning,
                        'bg-orange-100' => $account->is_disk_critical,
                        'bg-red-100' => $account->is_disk_full,
                        'bg-blue-200' => $account->suspended,
                        'bg-gray-50' => $loop->even && !($account->is_disk_warning || $account->is_disk_critical || $account->is_disk_full || $account->suspended),
                        'bg-white' => $loop->odd && !($account->is_disk_warning || $account->is_disk_critical || $account->is_disk_full || $account->suspended)
                    ])>
                <flux:table.cell class="px-6! py-5!">
                  <flux:link variant="subtle" :href="route('accounts.show', $account->id)">{{ $account->domain }}</flux:link>
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
                </flux:table.cell>

                <flux:table.cell class="whitespace-nowrap">{{ $account->wordpress_version ?? '—' }}</flux:table.cell>

                <flux:table.cell>
                  <flux:badge size="sm" :color="$account->backups_enabled ? 'green' : 'red'" inset="top bottom">{{ $account->backups_enabled ? 'Yes' : 'No'}}</flux:badge>
                </flux:table.cell>

                <flux:table.cell class="whitespace-nowrap">{{ $account->plan }}</flux:table.cell>

                <flux:table.cell class="whitespace-nowrap">{{ $account->disk_used }} / {{ $account->disk_limit }}</flux:table.cell>

                <flux:table.cell class="whitespace-nowrap">
                  {{ $account->formatted_disk_usage !== 'Unknown' ? $account->formatted_disk_usage : '—' }}
                </flux:table.cell>

                <flux:table.cell class="whitespace-nowrap">{{ $account->created_at->format('M d, Y') }}</flux:table.cell>

                <flux:table.cell>
                  @if($id = $this->getMonitorId($account->domain))
                    <flux:tooltip content="View Monitor">
                      <flux:button :href="route('monitors.show', $id)" size="sm" icon="magnifying-glass"></flux:button>
                    </flux:tooltip>
                  @endif
                </flux:table.cell>
              </flux:table.row>
            @empty
              <flux:table.row>
                <flux:table.cell colspan="8" class="py-8 whitespace-nowrap font-semibold text-zinc-700">
                  <div class="text-center">
                    <div class="flex items-center justify-center">
                      <flux:icon.magnifying-glass class="size-12" />
                    </div>
                    <p class="text-lg mt-6">No accounts were found.</p>
                  </div>
                </flux:table.cell>
              </flux:table.row>
            @endforelse
          </flux:table.rows>
        </flux:table>
      </flux:card>

    @endif

    <!-- /End Content -->
  </div>

  <!-- Delete Server Modal -->
  <flux:modal name="delete-server">
    <div class="space-y-6">
      <div class="sm:flex sm:items-start">
        <div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:size-10 dark:bg-red-500/10">
          <flux:icon.exclamation-triangle class="text-red-500" />
        </div>
        <div class="ml-4">
          <flux:heading size="lg">Delete server?</flux:heading>
          <flux:text class="mt-2">
            Are you sure you want to delete the server <span class="text-zinc-800 font-semibold">"{{ $server->name }}"</span>?
            The server information and all associated accounts will be permanently removed. This action cannot be undone.
          </flux:text>
        </div>
      </div>
      <div class="flex gap-2">
        <flux:spacer />
        <flux:modal.close>
          <flux:button>Cancel</flux:button>
        </flux:modal.close>
        <flux:button wire:click="delete" icon="trash" variant="danger">Delete server</flux:button>
      </div>
    </div>
  </flux:modal>
  <!-- /End Delete Server Modal -->

</div>
