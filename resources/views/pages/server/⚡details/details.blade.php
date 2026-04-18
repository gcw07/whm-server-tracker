<div>
  <!-- Page Header -->
  <div class="pb-5 border-b border-gray-200 md:flex md:items-center md:justify-between">
    <div class="flex-1 min-w-0">
      <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('dashboard')">Home</flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('servers.index')">Servers</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>Details</flux:breadcrumbs.item>
      </flux:breadcrumbs>

      <h3 class="mt-2 text-2xl font-semibold text-gray-900 tracking-tight text-balance">
        {{ $this->server->name }}
      </h3>
    </div>

    <div class="flex mt-3 md:mt-0 md:ml-4 gap-2">
      <flux:button :href="$this->server->whm_url" icon="arrow-top-right-on-square" target="_blank">View</flux:button>

      @if(!$this->server->missing_token)
        <flux:button wire:click="refresh" icon="arrow-path">Refresh</flux:button>
      @endif

      <flux:dropdown position="bottom" align="end">
        <flux:button icon="ellipsis-vertical" />

        <flux:menu>
          <flux:menu.item :href="route('servers.edit', $this->server)" icon="pencil-square">Edit</flux:menu.item>

          @if(!$this->server->missing_token)
            <flux:modal.trigger name="reset-token-modal">
              <flux:menu.item icon="key">Reset API Token</flux:menu.item>
            </flux:modal.trigger>
          @endif

          <flux:menu.separator />

          <flux:modal.trigger name="delete-server-modal">
            <flux:menu.item variant="danger" icon="trash">Delete</flux:menu.item>
          </flux:modal.trigger>
        </flux:menu>
      </flux:dropdown>
    </div>
  </div>
  <!-- / End Page Header -->


  <div class="mt-6">
    <!-- Begin content -->

    @if($this->server->missing_token)
      <flux:modal.trigger name="new-token-modal">
        <button type="button" class="relative block w-full rounded-lg border-2 border-dashed border-gray-300 p-12 text-center hover:border-gray-400 focus:outline-2 focus:outline-offset-2 focus:outline-blue-300 dark:border-white/15 dark:hover:border-white/25 dark:focus:outline-blue-500">
          <flux:icon.key class="mx-auto size-12 text-zinc-400 dark:text-gray-500" />
          <span class="mt-2 block text-sm font-semibold text-gray-900 dark:text-white">Add an API token to get started</span>
        </button>
      </flux:modal.trigger>
    @else
      <div class="hidden sm:flex sm:justify-end">
        <span class="inline-flex items-center gap-1.5 rounded-full border border-gray-200 bg-white px-2.5 py-1 text-xs">
          <flux:icon.clock class="size-3 shrink-0 text-gray-500" />
          <span class="text-gray-500">Last Updated</span>
          <span class="font-medium text-gray-800">
            @if($this->server->server_update_last_succeeded_at)
              {{ $this->server->server_update_last_succeeded_at->diffForHumans() }}
            @else
              Never
            @endif
          </span>
        </span>
      </div>
      <div class="hidden sm:block">
        <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3">

          {{-- Server Overview --}}
          <div class="relative overflow-hidden bg-white rounded-lg border border-gray-950/10 border-t-2 border-t-sky-500 px-6 py-5 flex flex-col gap-4">
            <flux:icon.info class="absolute -bottom-3 -right-3 size-28 text-sky-500 opacity-[0.08] pointer-events-none" />
            <div class="flex items-center justify-between gap-2">
              <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wide truncate">Server Overview</h3>
              @if(!$this->server->server_update_last_succeeded_at || $this->server->server_update_last_succeeded_at->diffInMinutes() > 120)
                <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-red-50 px-2.5 py-1 text-xs font-medium text-red-700"><span class="size-1.5 rounded-full bg-red-500"></span>Offline</span>
              @elseif($this->server->server_update_last_succeeded_at->diffInMinutes() > 60)
                <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700"><span class="size-1.5 rounded-full bg-amber-500"></span>Delayed</span>
              @else
                <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700"><span class="size-1.5 rounded-full bg-green-500"></span>Online</span>
              @endif
            </div>
            <div>
              <p class="text-2xl font-semibold text-gray-900 tabular-nums">{{ $this->server->accounts_count }}</p>
              <p class="text-xs text-gray-500 mt-1">Accounts</p>
            </div>
            <dl class="space-y-2 pt-3">
              <div class="flex items-start justify-between gap-4">
                <dt class="text-xs text-gray-500 shrink-0">Server type</dt>
                <dd class="text-xs font-medium text-gray-800 text-right">{{ $this->server->formatted_server_type }} &mdash; WHM {{ $this->server->formatted_whm_version }}</dd>
              </div>
              <div class="flex items-start justify-between gap-4">
                <dt class="text-xs text-gray-500 shrink-0">PHP</dt>
                <dd class="text-xs font-medium text-gray-800 text-right">
                  @foreach($this->server->formatted_php_installed_versions as $version)
                    {{ $version['version'] }}@if (!$loop->last), @endif
                  @endforeach &mdash; Sys {{ $this->server->formatted_php_system_version }}
                </dd>
              </div>
              <div class="flex items-center justify-between gap-4" x-data="{ copied: false }">
                <dt class="text-xs text-gray-500">IP address</dt>
                <flux:tooltip content="Click to copy">
                  <dd class="text-xs font-medium tabular-nums cursor-pointer select-none transition-colors"
                      x-bind:class="copied ? 'text-green-600' : 'text-gray-800 hover:text-sky-600'"
                      x-text="copied ? 'Copied!' : '{{ $this->server->address }}'"
                      x-on:click="
                          let ipEl = document.createElement('textarea');
                          ipEl.value = '{{ $this->server->address }}';
                          ipEl.style.position = 'fixed';
                          ipEl.style.opacity = '0';
                          document.body.appendChild(ipEl);
                          ipEl.select();
                          document.execCommand('copy');
                          document.body.removeChild(ipEl);
                          copied = true;
                          setTimeout(() => copied = false, 2000);
                      ">{{ $this->server->address }}</dd>
                </flux:tooltip>
              </div>
              <div class="flex items-center justify-between gap-4">
                <dt class="text-xs text-gray-500">Provider</dt>
                <dd class="text-xs font-medium text-gray-800">{{ $this->server->hosting_provider }}</dd>
              </div>
            </dl>
          </div>

          {{-- Disk --}}
          <div class="relative overflow-hidden bg-white rounded-lg border border-gray-950/10 border-t-2 border-t-violet-500 px-6 py-5 flex flex-col gap-4">
            <flux:icon.database class="absolute -bottom-3 -right-3 size-28 text-violet-500 opacity-[0.08] pointer-events-none" />
            <div class="flex items-center justify-between gap-2">
              <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wide truncate">Disk</h3>
              @if($this->server->settings->get('disk_percentage') >= 90)
                <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-red-50 px-2.5 py-1 text-xs font-medium text-red-700"><span class="size-1.5 rounded-full bg-red-500"></span>Critical</span>
              @elseif($this->server->settings->get('disk_percentage') >= 75)
                <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700"><span class="size-1.5 rounded-full bg-amber-500"></span>Warning</span>
              @else
                <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700"><span class="size-1.5 rounded-full bg-green-500"></span>Healthy</span>
              @endif
            </div>
            <div style="--disk-usage: {{ min($this->server->settings->get('disk_percentage', 0), 100) }}%">
              <p class="text-2xl font-semibold text-gray-900 tabular-nums">{{ $this->server->settings->get('disk_percentage') }}%</p>
              <p class="text-xs text-gray-500 mt-1">Used</p>
              <div class="mt-2 h-1.5 rounded-full bg-gray-100 overflow-hidden">
                <div class="h-full rounded-full w-(--disk-usage) @if($this->server->settings->get('disk_percentage') >= 90) bg-red-500 @elseif($this->server->settings->get('disk_percentage') >= 75) bg-amber-500 @else bg-violet-500 @endif"></div>
              </div>
            </div>
            <dl class="space-y-2 pt-3">
              <div class="flex items-center justify-between gap-4">
                <dt class="text-xs text-gray-500">Used</dt>
                <dd class="text-xs font-medium text-gray-800">{{ $this->server->formatted_disk_used }}</dd>
              </div>
              <div class="flex items-center justify-between gap-4">
                <dt class="text-xs text-gray-500">Available</dt>
                <dd class="text-xs font-medium text-gray-800">{{ $this->server->formatted_disk_available }}</dd>
              </div>
              <div class="flex items-center justify-between gap-4">
                <dt class="text-xs text-gray-500">Total</dt>
                <dd class="text-xs font-medium text-gray-800">{{ $this->server->formatted_disk_total }}</dd>
              </div>
            </dl>
          </div>

          {{-- Backups --}}
          <div class="relative overflow-hidden bg-white rounded-lg border border-gray-950/10 border-t-2 border-t-green-500 px-6 py-5 flex flex-col gap-4">
            <flux:icon.archive-restore class="absolute -bottom-3 -right-3 size-28 text-green-500 opacity-[0.08] pointer-events-none" />
            <div class="flex items-center justify-between gap-2">
              <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wide truncate">Backups</h3>
              @if($this->server->backups_enabled)
                <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700"><span class="size-1.5 rounded-full bg-green-500"></span>Enabled</span>
              @else
                <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-medium text-zinc-500"><span class="size-1.5 rounded-full bg-zinc-400"></span>Disabled</span>
              @endif
            </div>
            <div>
              <p class="text-2xl font-semibold text-gray-900 tabular-nums">{{ $this->server->backup_types_active_count }} / 3</p>
              <p class="text-xs text-gray-500 mt-1">Types active</p>
            </div>
            <dl class="space-y-2 pt-3">
              <div class="flex items-center justify-between gap-4">
                <dt class="text-xs font-semibold text-gray-700">Daily</dt>
                <dd class="text-xs font-medium text-gray-800 text-right">
                  @if($this->server->settings->get('backup_daily_enabled'))
                    {{ $this->server->settings->get('backup_daily_retention') }} kept &middot; {{ $this->server->formatted_backup_daily_days }}
                  @else
                    <span class="text-gray-400">Disabled</span>
                  @endif
                </dd>
              </div>
              <div class="flex items-center justify-between gap-4">
                <dt class="text-xs font-semibold text-gray-700">Weekly</dt>
                <dd class="text-xs font-medium text-gray-800 text-right">
                  @if($this->server->settings->get('backup_weekly_enabled'))
                    {{ $this->server->settings->get('backup_weekly_retention') }} kept &middot; {{ $this->server->formatted_backup_weekly_day }}
                  @else
                    <span class="text-gray-400">Disabled</span>
                  @endif
                </dd>
              </div>
              <div class="flex items-center justify-between gap-4">
                <dt class="text-xs font-semibold text-gray-700">Monthly</dt>
                <dd class="text-xs font-medium text-gray-800 text-right">
                  @if($this->server->settings->get('backup_monthly_enabled'))
                    {{ $this->server->settings->get('backup_monthly_retention') }} kept &middot; {{ $this->server->formatted_backup_monthly_days }}
                  @else
                    <span class="text-gray-400">Disabled</span>
                  @endif
                </dd>
              </div>
            </dl>
          </div>
        </div>
        <div class="mt-4 rounded-lg border border-gray-950/10 bg-white px-6 py-4 flex gap-4">
          <span class="text-sm font-semibold text-gray-700 shrink-0">Notes</span>
          <p class="text-sm text-gray-600 text-pretty">{{ $this->server->notes }}</p>
        </div>
      </div>

      <flux:card class="p-0 overflow-hidden bg-gray-50 mt-8">
        <flux:table>
          <flux:table.columns>
            <flux:table.column class="px-6! bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">DOMAIN</flux:table.column>
            <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">EMAILS</flux:table.column>
            <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">PLAN</flux:table.column>
            <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">PHP</flux:table.column>
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
                  @if(!$account->backups_enabled)
                    <flux:badge size="sm" color="red" inset="top bottom" class="ml-1">No Backups</flux:badge>
                  @endif
                </flux:table.cell>

                <flux:table.cell class="whitespace-nowrap">
                  @if($account->emails_count === 1)
                    <flux:badge size="sm" color="zinc">None</flux:badge>
                  @else
                    {{ $account->emails_count }}
                  @endif
                </flux:table.cell>

                <flux:table.cell class="whitespace-nowrap">{{ $account->plan }}</flux:table.cell>

                <flux:table.cell class="whitespace-nowrap">
                  @if($account->php_version)
                    <flux:badge size="sm" :color="$account->formatted_php_version['color']" inset="top bottom">{{ $account->formatted_php_version['version'] }}</flux:badge>
                  @else
                    —
                  @endif
                </flux:table.cell>

                <flux:table.cell class="whitespace-nowrap">{{ $account->disk_used }} / {{ $account->disk_limit }}</flux:table.cell>

                <flux:table.cell class="whitespace-nowrap">
                  {{ $account->formatted_disk_usage !== 'Unknown' ? $account->formatted_disk_usage : '—' }}
                </flux:table.cell>

                <flux:table.cell class="whitespace-nowrap">{{ $account->created_at->format('M d, Y') }}</flux:table.cell>

                <flux:table.cell>
                  @if($account->monitor_id)
                    <flux:tooltip content="View Monitor">
                      <flux:button :href="route('monitors.show', $account->monitor_id)" size="sm" icon="magnifying-glass"></flux:button>
                    </flux:tooltip>
                  @endif
                </flux:table.cell>
              </flux:table.row>
            @empty
              <flux:table.row>
                <flux:table.cell colspan="9" class="py-8 whitespace-nowrap font-semibold text-zinc-700">
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

  <!--New Token Modal -->
  <flux:modal name="new-token-modal" class="md:w-3xl">
    <form wire:submit="saveNewApiToken">
      <div class="space-y-6">
        <div class="sm:flex sm:items-start">
          <div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-green-100 sm:mx-0 sm:size-10 dark:bg-green-500/10">
            <flux:icon.key class="text-green-500" />
          </div>
          <div class="ml-4 w-full">
            <flux:heading size="lg">New API Token</flux:heading>
            <flux:input wire:model="newToken" placeholder="WHM Token" class="mt-6" />
            <flux:error name="newToken" />
          </div>
        </div>
        <div class="flex gap-2">
          <flux:spacer />
          <flux:modal.close>
            <flux:button>Cancel</flux:button>
          </flux:modal.close>
          <flux:button type="submit" icon="check" variant="primary">Save token</flux:button>
        </div>
      </div>
    </form>
  </flux:modal>
  <!-- /End New Token Modal -->

  <!--Reset Token Modal -->
  <flux:modal name="reset-token-modal">
    <div class="space-y-6">
      <div class="sm:flex sm:items-start">
        <div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:size-10 dark:bg-red-500/10">
          <flux:icon.exclamation-triangle class="text-red-500" />
        </div>
        <div class="ml-4">
          <flux:heading size="lg">Reset API Token</flux:heading>
          <flux:text class="mt-2">
            Are you sure you want to reset the api token for the server <span class="text-zinc-800 font-semibold">"{{ $this->server->name }}"</span>?
            This action cannot be undone.
          </flux:text>
        </div>
      </div>
      <div class="flex gap-2">
        <flux:spacer />
        <flux:modal.close>
          <flux:button>Cancel</flux:button>
        </flux:modal.close>
        <flux:button wire:click="resetApiToken" icon="check" variant="danger">Confirm</flux:button>
      </div>
    </div>
  </flux:modal>
  <!-- /End Delete Server Modal -->

  <!-- Delete Server Modal -->
  <flux:modal name="delete-server-modal">
    <div class="space-y-6">
      <div class="sm:flex sm:items-start">
        <div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:size-10 dark:bg-red-500/10">
          <flux:icon.exclamation-triangle class="text-red-500" />
        </div>
        <div class="ml-4">
          <flux:heading size="lg">Delete server?</flux:heading>
          <flux:text class="mt-2">
            Are you sure you want to delete the server <span class="text-zinc-800 font-semibold">"{{ $this->server->name }}"</span>?
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
