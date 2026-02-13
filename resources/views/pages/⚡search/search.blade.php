<div>
  <!-- Page Header -->
  <div class="pb-3 border-b border-gray-200 sm:flex sm:items-center sm:justify-between">
    <h3 class="text-2xl leading-6 font-medium text-gray-900">
      Search
    </h3>
    <div class="mt-3 flex md:mt-0 gap-2">
    </div>
  </div>
  <!-- / End Page Header -->

  <div class="mt-6">
    <!-- Begin content -->

    <div>
      <flux:input.group>
        <flux:input wire:model.live.debounce.500ms="search" placeholder="Search..." clearable />
        <flux:button icon="magnifying-glass"></flux:button>
      </flux:input.group>
    </div>

    @if($search)
      <div class="mt-4">
        <p class="text-sm">
          {{ $this->servers->count() }} results for servers matching <b>{{ $search }}</b> sorted by alphabetically.
        </p>
        <p class="text-sm">
          {{ $this->accounts->count() }} results for accounts matching <b>{{ $search }}</b> sorted by alphabetically.
        </p>
        <p class="text-sm">
          {{ $this->monitors->count() }} results for accounts matching <b>{{ $search }}</b> sorted by alphabetically.
        </p>
      </div>

      <div class="mt-8 pb-3 border-b border-gray-200 sm:flex sm:items-center sm:justify-between">
        <h3 class="text-xl leading-6 font-medium text-gray-900">
          Servers
        </h3>
      </div>

      <flux:card class="mt-6 p-0 overflow-hidden bg-gray-50">
        <flux:table>
          <flux:table.columns>
            <flux:table.column class="px-6! bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">NAME</flux:table.column>
            <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">ACCOUNTS</flux:table.column>
            <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">BACKUPS</flux:table.column>
            <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">PHP</flux:table.column>
            <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">USAGE</flux:table.column>
            <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">SETUP DATE</flux:table.column>
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

      <div class="mt-8 pb-3 border-b border-gray-200 sm:flex sm:items-center sm:justify-between">
        <h3 class="text-xl leading-6 font-medium text-gray-900">
          Accounts
        </h3>
      </div>

      <flux:card class="mt-6 p-0 overflow-hidden bg-gray-50">
        <flux:table>
          <flux:table.columns>
            <flux:table.column class="px-6! bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">DOMAIN</flux:table.column>
            <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">WORDPRESS</flux:table.column>
            <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">BACKUPS</flux:table.column>
            <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">USED / LIMIT</flux:table.column>
            <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">USAGE</flux:table.column>
            <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">DATE ADDED</flux:table.column>
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

      <div class="mt-8 pb-3 border-b border-gray-200 sm:flex sm:items-center sm:justify-between">
        <h3 class="text-xl leading-6 font-medium text-gray-900">
          Monitors
        </h3>
      </div>

      <flux:card class="mt-6 p-0 overflow-hidden bg-gray-50">
        <flux:table>
          <flux:table.columns>
            <flux:table.column class="px-6! bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">SITE</flux:table.column>
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

    @else
      <div class="text-center mt-10">Start typing above to search server, account and monitor listings.</div>
    @endif

    <!-- /End Content -->
  </div>

</div>
