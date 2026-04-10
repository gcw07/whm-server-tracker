<div>
  <!-- Page Header -->
  <div class="pb-5 border-b border-gray-200 md:flex md:items-center md:justify-between">
    <div class="flex-1 min-w-0">
      <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('dashboard')">Home</flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('monitors.index')">Monitors</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>Details</flux:breadcrumbs.item>
      </flux:breadcrumbs>

      <h3 class="mt-2 text-2xl leading-6 font-medium text-gray-900">
        {{ $this->monitor->domain_name }}
      </h3>
    </div>

    <div class="flex mt-3 md:mt-0 md:ml-4 gap-2">
      <flux:button :href="$this->monitor->url" icon="arrow-top-right-on-square" target="_blank">View</flux:button>

      <flux:dropdown position="bottom" align="end">
        <flux:button icon="arrow-path" icon:trailing="chevron-down">Refresh</flux:button>

        <flux:menu>
          <flux:menu.item wire:click="refreshWordPressCheck" icon="globe-alt">WordPress</flux:menu.item>
          <flux:menu.item wire:click="refreshBlacklistCheck" icon="envelope">Email Blacklist</flux:menu.item>
          <flux:menu.item wire:click="refreshDomainInfoCheck" icon="identification">Domain Info</flux:menu.item>
          <flux:menu.item wire:click="refreshLighthouseCheck" icon="light-bulb">Lighthouse Report</flux:menu.item>
        </flux:menu>
      </flux:dropdown>

      <flux:dropdown position="bottom" align="end">
        <flux:button icon="ellipsis-vertical" />

        <flux:menu>
          <flux:menu.item wire:click="enableAllMonitors" icon="bell">Enable All Monitors</flux:menu.item>
          <flux:menu.item wire:click="disableAllMonitors" icon="bell-slash">Disable All Monitors</flux:menu.item>
        </flux:menu>
      </flux:dropdown>
    </div>
  </div>
  <!-- / End Page Header -->

  <!-- Status Overview Strip -->
  <flux:card class="mt-6 px-6 py-4">
    <div class="flex flex-wrap gap-3 items-center">
      <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Checks</span>

      {{-- Uptime --}}
      @if(!$this->monitor->uptime_check_enabled)
        <flux:badge as="a" href="#uptime-card" color="zinc" icon="no-symbol">Uptime</flux:badge>
      @elseif($this->monitor->uptime_status === 'not yet checked')
        <flux:badge as="a" href="#uptime-card" color="yellow" icon="clock">Uptime</flux:badge>
      @elseif($this->monitor->uptime_status === 'down')
        <flux:badge as="a" href="#uptime-card" color="red" icon="arrow-down">Uptime</flux:badge>
      @else
        <flux:badge as="a" href="#uptime-card" color="green" icon="check">Uptime</flux:badge>
      @endif

      {{-- SSL --}}
      @if(!$this->monitor->certificate_check_enabled)
        <flux:badge as="a" href="#ssl-card" color="zinc" icon="no-symbol">SSL</flux:badge>
      @elseif($this->sslCertificates->isEmpty())
        <flux:badge as="a" href="#ssl-card" color="zinc" icon="lock-closed">SSL</flux:badge>
      @elseif($this->sslCertificates->contains(fn($c) => $c->expires_at?->isPast()))
        <flux:badge as="a" href="#ssl-card" color="red" icon="x-circle">SSL</flux:badge>
      @elseif($this->sslCertificates->contains(fn($c) => $c->expires_at && now()->diffInDays($c->expires_at) <= 29))
        <flux:badge as="a" href="#ssl-card" color="amber" icon="exclamation-triangle">SSL</flux:badge>
      @else
        <flux:badge as="a" href="#ssl-card" color="green" icon="lock-closed">SSL</flux:badge>
      @endif

      {{-- Blacklist --}}
      @if(!$this->monitor->blacklistCheck?->enabled)
        <flux:badge as="a" href="#blacklist-card" color="zinc" icon="no-symbol">Blacklist</flux:badge>
      @elseif($this->monitor->blacklistCheck?->status->value === 'not yet checked')
        <flux:badge as="a" href="#blacklist-card" color="yellow" icon="clock">Blacklist</flux:badge>
      @elseif($this->monitor->blacklistCheck?->status->value === 'invalid')
        <flux:badge as="a" href="#blacklist-card" color="red" icon="exclamation-triangle">Blacklist</flux:badge>
      @else
        <flux:badge as="a" href="#blacklist-card" color="green" icon="envelope">Blacklist</flux:badge>
      @endif

      {{-- WordPress --}}
      @if(!$this->monitor->wordpressCheck?->enabled)
        <flux:badge as="a" href="#wordpress-card" color="zinc" icon="no-symbol">WordPress</flux:badge>
      @elseif($this->monitor->wordpressCheck?->status->value === 'not yet checked')
        <flux:badge as="a" href="#wordpress-card" color="yellow" icon="clock">WordPress</flux:badge>
      @elseif($this->monitor->wordpressCheck?->status->value === 'invalid')
        <flux:badge as="a" href="#wordpress-card" color="red" icon="exclamation-triangle">WordPress</flux:badge>
      @else
        <flux:badge as="a" href="#wordpress-card" color="green" icon="globe-alt">WordPress</flux:badge>
      @endif

      {{-- Domain --}}
      @if(!$this->monitor->domainCheck?->enabled)
        <flux:badge as="a" href="#domain-card" color="zinc" icon="no-symbol">Domain</flux:badge>
      @elseif($this->monitor->domainCheck?->status->value === 'not yet checked')
        <flux:badge as="a" href="#domain-card" color="yellow" icon="clock">Domain</flux:badge>
      @elseif($this->monitor->domainCheck?->status->value === 'invalid')
        <flux:badge as="a" href="#domain-card" color="red" icon="x-circle">Domain</flux:badge>
      @else
        <flux:badge as="a" href="#domain-card" color="green" icon="identification">Domain</flux:badge>
      @endif

      {{-- Lighthouse --}}
      @if(!$this->monitor->lighthouseCheck?->enabled)
        <flux:badge as="a" href="#lighthouse-card" color="zinc" icon="no-symbol">Lighthouse</flux:badge>
      @elseif($this->monitor->lighthouseCheck?->status->value === 'not yet checked')
        <flux:badge as="a" href="#lighthouse-card" color="yellow" icon="clock">Lighthouse</flux:badge>
      @elseif($this->monitor->lighthouseCheck?->status->value === 'invalid')
        <flux:badge as="a" href="#lighthouse-card" color="red" icon="exclamation-triangle">Lighthouse</flux:badge>
      @else
        <flux:badge as="a" href="#lighthouse-card" color="green" icon="light-bulb">Lighthouse</flux:badge>
      @endif
    </div>
  </flux:card>
  <!-- / End Status Overview Strip -->

  <!-- Two-Column Layout -->
  <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

    <!-- Left Column (primary checks) -->
    <div class="lg:col-span-2 flex flex-col gap-6">

      <!-- Details Card -->
      <flux:card class="p-0 overflow-hidden divide-y divide-gray-100" id="details-card">
        <div class="flex items-center px-5 py-4">
          <div class="flex items-center gap-3 border-l-4 border-gray-400 pl-3">
            <flux:icon.server class="size-4 text-gray-500 shrink-0" />
            <flux:heading level="3" class="text-sm! font-semibold text-gray-800 m-0!">Details</flux:heading>
          </div>
        </div>

        @if($this->monitor->accounts->count() > 1)
          <div class="flex items-center gap-2 px-5 py-3 bg-red-50 text-sm font-medium text-red-700">
            <flux:icon.exclamation-triangle variant="solid" class="size-4 shrink-0" />
            This domain was found on multiple servers.
          </div>
        @endif

        <dl class="divide-y divide-gray-100">
          <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 px-5">
            <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Server</dt>
            <dd class="text-sm font-medium text-gray-800 sm:mt-0 sm:col-span-2">
              <ul role="list" class="divide-y divide-gray-100">
                @foreach($this->monitor->accounts as $account)
                  <li class="flex items-center justify-between py-3 first:pt-0 last:pb-0">
                    <div class="flex items-center gap-2">
                      <flux:link variant="subtle" :href="route('servers.show', $account->server->id)">
                        {{ $account->server->name }}
                      </flux:link>
                      @if($account->suspended)
                        <flux:dropdown position="bottom" align="start">
                          <flux:badge as="button" size="sm" color="blue" inset="top bottom" icon:trailing="information-circle">Suspended</flux:badge>

                          <flux:popover class="flex flex-col gap-3 rounded-xl shadow-xl">
                            <div>
                              This account was suspended on {{ $account->suspend_time->format('F d, Y \a\t g:ia') }}. It was suspended for "{{ $account->suspend_reason }}".
                            </div>
                          </flux:popover>
                        </flux:dropdown>
                      @endif
                    </div>
                  </li>
                @endforeach
              </ul>
            </dd>
          </div>
          <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 px-5">
            <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Disk Usage</dt>
            <dd class="text-sm font-medium text-gray-800 sm:mt-0 sm:col-span-2">
              {{ $this->monitor->accounts->first()?->formatted_disk_usage }}
            </dd>
          </div>
        </dl>
      </flux:card>
      <!-- End Details Card -->

      <!-- Uptime Checks Card -->
      <flux:card class="p-0 overflow-hidden divide-y divide-gray-100" id="uptime-card">
        <div class="flex items-center justify-between px-5 py-4">
          <div class="flex items-center gap-3 border-l-4 border-cyan-500 pl-3">
            <flux:icon.check-badge class="size-4 text-cyan-600 shrink-0" />
            <flux:heading level="3" class="text-sm! font-semibold text-gray-800 m-0!">Uptime Checks</flux:heading>
          </div>
          <flux:tooltip :content="$this->monitor->uptime_check_enabled ? 'Disable check' : 'Enable check'">
            <flux:button
              wire:click="toggleUptimeCheck"
              variant="subtle"
              size="sm"
              :icon="$this->monitor->uptime_check_enabled ? 'check-circle' : 'x-circle'"
              :class="$this->monitor->uptime_check_enabled ? 'text-emerald-600!' : 'text-gray-300!'"
            />
          </flux:tooltip>
        </div>

        @if(!$this->monitor->uptime_check_enabled)
          <div class="px-5 py-8 flex flex-col items-center gap-2 text-center">
            <flux:icon.no-symbol class="size-8 text-gray-300" />
            <p class="text-sm text-gray-400">Uptime check is currently disabled.</p>
            <flux:button wire:click="toggleUptimeCheck" variant="ghost" size="sm" icon="check-circle">Enable check</flux:button>
          </div>
        @else
          <dl class="divide-y divide-gray-100">
            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 px-5">
              <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Current Status</dt>
              <dd class="text-sm sm:mt-0 sm:col-span-2">
                @if($this->monitor->uptime_status === 'down')
                  <flux:badge size="sm" color="red" icon="arrow-down">Down</flux:badge>
                @elseif($this->monitor->uptime_status === 'not yet checked')
                  <flux:badge size="sm" color="yellow" icon="clock">Pending</flux:badge>
                @else
                  <flux:badge size="sm" color="green" icon="check">Up</flux:badge>
                @endif
              </dd>
            </div>
            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 px-5">
              <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Last Checked</dt>
              <dd class="text-sm font-medium text-gray-800 sm:mt-0 sm:col-span-2">{{ $this->monitor->uptime_last_check_date?->diffForHumans() ?? 'Never' }}</dd>
            </div>
            @if($this->monitor->uptime_status === 'down')
              <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 px-5">
                <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Down Since</dt>
                <dd class="text-sm font-medium text-gray-800 sm:mt-0 sm:col-span-2">{{ $this->monitor->uptime_status_last_change_date?->diffForHumans() ?? 'Unknown' }}</dd>
              </div>
              <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 px-5">
                <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Failure Reason</dt>
                <dd class="text-sm font-medium text-gray-800 sm:mt-0 sm:col-span-2">{{ $this->monitor->uptime_check_failure_reason }}</dd>
              </div>
            @endif
          </dl>

          {{-- Uptime percentage tiles --}}
          @php
            $today = $this->monitor->uptime_for_today;
            $seven = $this->monitor->uptime_for_last_seven_days;
            $thirty = $this->monitor->uptime_for_last_thirty_days;
          @endphp
          <div class="px-5 py-4 grid grid-cols-3 gap-3">
            <div @class([
              'rounded-lg border p-3 text-center',
              'bg-green-50 border-green-200' => $today >= 90,
              'bg-amber-50 border-amber-200' => $today >= 70 && $today < 90,
              'bg-red-50 border-red-200'     => $today < 70,
            ])>
              <div class="text-xs font-medium text-gray-500 mb-1">Today</div>
              <div @class([
                'text-2xl font-bold',
                'text-green-700' => $today >= 90,
                'text-amber-700' => $today >= 70 && $today < 90,
                'text-red-700'   => $today < 70,
              ])>{{ $today }}%</div>
            </div>
            <div @class([
              'rounded-lg border p-3 text-center',
              'bg-green-50 border-green-200' => $seven >= 90,
              'bg-amber-50 border-amber-200' => $seven >= 70 && $seven < 90,
              'bg-red-50 border-red-200'     => $seven < 70,
            ])>
              <div class="text-xs font-medium text-gray-500 mb-1">Last 7 Days</div>
              <div @class([
                'text-2xl font-bold',
                'text-green-700' => $seven >= 90,
                'text-amber-700' => $seven >= 70 && $seven < 90,
                'text-red-700'   => $seven < 70,
              ])>{{ $seven }}%</div>
            </div>
            <div @class([
              'rounded-lg border p-3 text-center',
              'bg-green-50 border-green-200' => $thirty >= 90,
              'bg-amber-50 border-amber-200' => $thirty >= 70 && $thirty < 90,
              'bg-red-50 border-red-200'     => $thirty < 70,
            ])>
              <div class="text-xs font-medium text-gray-500 mb-1">Last 30 Days</div>
              <div @class([
                'text-2xl font-bold',
                'text-green-700' => $thirty >= 90,
                'text-amber-700' => $thirty >= 70 && $thirty < 90,
                'text-red-700'   => $thirty < 70,
              ])>{{ $thirty }}%</div>
            </div>
          </div>

          {{-- Uptime chart --}}
          <div class="px-5 pt-4 pb-1">
            <div class="flex items-center gap-1.5 pb-3">
              @foreach(['7' => '7d', '30' => '30d', '90' => '90d'] as $value => $label)
                <button
                  wire:click="$set('uptimePeriod', '{{ $value }}')"
                  @class([
                    'px-3 py-1 rounded-md font-semibold transition cursor-pointer',
                    'bg-cyan-600 text-white text-xs' => $this->uptimePeriod === (string) $value,
                    'bg-gray-100 text-gray-500 hover:bg-gray-200 hover:text-gray-700 text-xs' => $this->uptimePeriod !== (string) $value,
                  ])
                >{{ $label }}</button>
              @endforeach
            </div>

            <flux:chart :value="$this->uptimeChartData" class="aspect-6/1 w-full pb-5">
              <flux:chart.svg>
                <flux:chart.stack>
                  <flux:chart.bar field="uptime" class="text-green-500/85" />
                  <flux:chart.bar field="downtime" class="text-red-500/85" />
                </flux:chart.stack>
                <flux:chart.axis axis="x" field="date" />
                <flux:chart.axis axis="y" :tick-values="[0, 25, 50, 75, 100]" />
                <flux:chart.cursor />
              </flux:chart.svg>
              <flux:chart.tooltip>
                <flux:chart.tooltip.heading field="date" />
                <flux:chart.tooltip.value field="uptime" label="Uptime" suffix="%" />
                <flux:chart.tooltip.value field="downtime" label="Downtime" suffix="%" />
              </flux:chart.tooltip>
            </flux:chart>
            <div class="flex justify-between text-xs text-gray-400 pt-1 pb-4">
              <span>{{ now()->subDays((int) $this->uptimePeriod - 1)->format('M j') }}</span>
              <span>Today</span>
            </div>
          </div>
        @endif
      </flux:card>
      <!-- End Uptime Checks Card -->

      <!-- SSL Certificates Card -->
      <flux:card class="p-0 overflow-hidden divide-y divide-gray-100" id="ssl-card">
        <div class="flex items-center justify-between px-5 py-4">
          <div class="flex items-center gap-3 border-l-4 border-green-500 pl-3">
            <flux:icon.lock-closed class="size-4 text-green-600 shrink-0" />
            <flux:heading level="3" class="text-sm! font-semibold text-gray-800 m-0!">SSL Certificates</flux:heading>
          </div>
          <flux:tooltip :content="$this->monitor->certificate_check_enabled ? 'Disable check' : 'Enable check'">
            <flux:button
              wire:click="toggleCertificateCheck"
              variant="subtle"
              size="sm"
              :icon="$this->monitor->certificate_check_enabled ? 'check-circle' : 'x-circle'"
              :class="$this->monitor->certificate_check_enabled ? 'text-emerald-600!' : 'text-gray-300!'"
            />
          </flux:tooltip>
        </div>

        @if(!$this->monitor->certificate_check_enabled)
          <div class="px-5 py-8 flex flex-col items-center gap-2 text-center">
            <flux:icon.no-symbol class="size-8 text-gray-300" />
            <p class="text-sm text-gray-400">SSL Certificate check is currently disabled.</p>
            <flux:button wire:click="toggleCertificateCheck" variant="ghost" size="sm" icon="check-circle">Enable check</flux:button>
          </div>
        @elseif($this->sslCertificates->isEmpty())
          <div class="px-5 py-6 text-sm text-gray-400 text-center">No SSL certificates found.</div>
        @else
          <dl class="divide-y divide-gray-100">
            @foreach($this->sslCertificates as $cert)
              <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-10 px-5">
                <dt class="text-sm font-medium text-gray-800">
                  {{ $cert->servername }}
                  <div class="mt-1">
                    @if($cert->type->value === 'main')
                      <flux:badge size="sm" color="blue">Main</flux:badge>
                    @else
                      <flux:badge size="sm" color="zinc">Subdomain</flux:badge>
                    @endif
                  </div>
                </dt>
                <dd class="mt-1 sm:mt-0 sm:col-span-2">
                  @if($cert->expires_at === null)
                    <span class="text-sm text-gray-400">Expiration unknown</span>
                  @elseif($cert->expires_at->isPast())
                    <flux:badge size="sm" color="red">Expired {{ $cert->expires_at->format('M j, Y') }}</flux:badge>
                  @elseif(now()->diffInDays($cert->expires_at) <= 29)
                    <flux:badge size="sm" color="amber">Expires in {{ (int) now()->diffInDays($cert->expires_at) }} days ({{ $cert->expires_at->format('M j, Y') }})</flux:badge>
                  @else
                    <flux:badge size="sm" color="green">Valid until {{ $cert->expires_at->format('M j, Y') }}</flux:badge>
                  @endif
                  @if($cert->issuer)
                    <div class="mt-1 text-xs text-gray-400">{{ $cert->issuer }}</div>
                  @endif
                  @if($cert->vhost_domains)
                    <ul class="mt-3 space-y-1">
                      @foreach($cert->vhost_domains as $domain)
                        <li class="flex items-center gap-1.5 text-xs">
                          @if(in_array($domain, $cert->certificate_domains ?? []))
                            <flux:icon.check-circle variant="solid" class="size-3.5 text-green-500 shrink-0" />
                            <span class="text-gray-700">{{ $domain }}</span>
                          @else
                            <flux:icon.x-circle variant="solid" class="size-3.5 text-red-500 shrink-0" />
                            <span class="text-gray-400">{{ $domain }}</span>
                          @endif
                        </li>
                      @endforeach
                    </ul>
                  @endif
                </dd>
              </div>
            @endforeach
          </dl>
        @endif
      </flux:card>
      <!-- End SSL Certificates Card -->

      <!-- Email Blacklist Card -->
      <flux:card class="p-0 overflow-hidden divide-y divide-gray-100" id="blacklist-card">
        <div class="flex items-center justify-between px-5 py-4">
          <div class="flex items-center gap-3 border-l-4 border-amber-500 pl-3">
            <flux:icon.envelope class="size-4 text-amber-600 shrink-0" />
            <flux:heading level="3" class="text-sm! font-semibold text-gray-800 m-0!">Email Blacklist</flux:heading>
          </div>
          <flux:tooltip :content="$this->monitor->blacklistCheck?->enabled ? 'Disable check' : 'Enable check'">
            <flux:button
              wire:click="toggleBlacklistCheck"
              variant="subtle"
              size="sm"
              :icon="$this->monitor->blacklistCheck?->enabled ? 'check-circle' : 'x-circle'"
              :class="$this->monitor->blacklistCheck?->enabled ? 'text-emerald-600!' : 'text-gray-300!'"
            />
          </flux:tooltip>
        </div>

        @if(!$this->monitor->blacklistCheck?->enabled)
          <div class="px-5 py-8 flex flex-col items-center gap-2 text-center">
            <flux:icon.no-symbol class="size-8 text-gray-300" />
            <p class="text-sm text-gray-400">Email Blacklist check is currently disabled.</p>
            <flux:button wire:click="toggleBlacklistCheck" variant="ghost" size="sm" icon="check-circle">Enable check</flux:button>
          </div>
        @else
          <dl class="divide-y divide-gray-100">
            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 px-5">
              <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Current Status</dt>
              <dd class="text-sm sm:mt-0 sm:col-span-2">
                @if($this->monitor->blacklistCheck?->status->value === 'invalid')
                  <flux:badge size="sm" color="red" icon="exclamation-triangle">Found on Blacklist</flux:badge>
                @elseif($this->monitor->blacklistCheck?->status->value === 'not yet checked')
                  <flux:badge size="sm" color="yellow" icon="clock">Pending</flux:badge>
                @else
                  <flux:badge size="sm" color="green" icon="check">Not Listed</flux:badge>
                @endif
              </dd>
            </div>
            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 px-5">
              <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Last Checked</dt>
              <dd class="text-sm font-medium text-gray-800 sm:mt-0 sm:col-span-2">{{ $this->monitor->blacklistCheck?->results->max('checked_at')?->diffForHumans() ?? 'Never' }}</dd>
            </div>
            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 px-5">
              <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Lists</dt>
              <dd class="text-sm sm:mt-0 sm:col-span-2">
                <div class="space-y-2">
                  @foreach($this->monitor->blacklistCheck->results->sortBy('driver') as $result)
                    <div class="flex items-center justify-between">
                      <span class="text-sm text-gray-700">
                        @if($result->url)
                          <flux:link href="{{ $result->url }}" external>{{ $result->driver }} <flux:icon.arrow-top-right-on-square class="inline size-3.5" /></flux:link>
                        @else
                          {{ $result->driver }}
                        @endif
                      </span>
                      @if($result->checked_at === null)
                        <flux:badge size="sm" color="yellow" icon="clock">Pending</flux:badge>
                      @elseif($result->listed)
                        <flux:tooltip content="Checked: {{ $result->checked_value }}">
                          <flux:badge size="sm" color="red" icon="exclamation-triangle" class="cursor-pointer">Listed</flux:badge>
                        </flux:tooltip>
                      @else
                        <flux:tooltip content="Checked: {{ $result->checked_value }}">
                          <flux:badge size="sm" color="green" icon="check" class="cursor-pointer">Clean</flux:badge>
                        </flux:tooltip>
                      @endif
                    </div>
                  @endforeach
                </div>
              </dd>
            </div>
          </dl>
        @endif
      </flux:card>
      <!-- End Email Blacklist Card -->

    </div>
    <!-- End Left Column -->

    <!-- Right Column (secondary checks) -->
    <div class="flex flex-col gap-6">

      <!-- WordPress Card -->
      <flux:card class="p-0 overflow-hidden divide-y divide-gray-100" id="wordpress-card">
        <div class="flex items-center justify-between px-5 py-4">
          <div class="flex items-center gap-3 border-l-4 border-blue-500 pl-3">
            <flux:icon.globe-alt class="size-4 text-blue-600 shrink-0" />
            <flux:heading level="3" class="text-sm! font-semibold text-gray-800 m-0!">WordPress</flux:heading>
          </div>
          <flux:tooltip :content="$this->monitor->wordpressCheck?->enabled ? 'Disable check' : 'Enable check'">
            <flux:button
              wire:click="toggleWordPressCheck"
              variant="subtle"
              size="sm"
              :icon="$this->monitor->wordpressCheck?->enabled ? 'check-circle' : 'x-circle'"
              :class="$this->monitor->wordpressCheck?->enabled ? 'text-emerald-600!' : 'text-gray-300!'"
            />
          </flux:tooltip>
        </div>

        @if(!$this->monitor->wordpressCheck?->enabled)
          <div class="px-5 py-8 flex flex-col items-center gap-2 text-center">
            <flux:icon.no-symbol class="size-8 text-gray-300" />
            <p class="text-sm text-gray-400">WordPress check is currently disabled.</p>
            <flux:button wire:click="toggleWordPressCheck" variant="ghost" size="sm" icon="check-circle">Enable check</flux:button>
          </div>
        @else
          <dl class="divide-y divide-gray-100">
            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 px-5">
              <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Status</dt>
              <dd class="text-sm sm:mt-0 sm:col-span-2">
                @if($this->monitor->wordpressCheck?->status->value === 'invalid')
                  <flux:badge size="sm" color="red" icon="x-circle">Error</flux:badge>
                @elseif($this->monitor->wordpressCheck?->status->value === 'not yet checked')
                  <flux:badge size="sm" color="yellow" icon="clock">Pending</flux:badge>
                @else
                  <flux:badge size="sm" color="green" icon="check">Ok</flux:badge>
                @endif
              </dd>
            </div>
            @if($this->monitor->wordpressCheck?->status->value === 'valid')
              <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 px-5">
                <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Version</dt>
                <dd class="text-sm font-medium text-gray-800 sm:mt-0 sm:col-span-2">{{ $this->monitor->wordpressCheck?->wordpress_version ?: 'WP not detected' }}</dd>
              </div>
            @endif
            @if($this->monitor->wordpressCheck?->status->value === 'invalid')
              <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 px-5">
                <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Error</dt>
                <dd class="text-sm font-medium text-gray-800 sm:mt-0 sm:col-span-2">{{ $this->monitor->wordpressCheck?->failure_reason }}</dd>
              </div>
            @endif
          </dl>
        @endif
      </flux:card>
      <!-- End WordPress Card -->

      <!-- Domain Information Card -->
      <flux:card class="p-0 overflow-hidden divide-y divide-gray-100" id="domain-card">
        <div class="flex items-center justify-between px-5 py-4">
          <div class="flex items-center gap-3 border-l-4 border-violet-500 pl-3">
            <flux:icon.identification class="size-4 text-violet-600 shrink-0" />
            <flux:heading level="3" class="text-sm! font-semibold text-gray-800 m-0!">Domain Information</flux:heading>
          </div>
          <flux:tooltip :content="$this->monitor->domainCheck?->enabled ? 'Disable check' : 'Enable check'">
            <flux:button
              wire:click="toggleDomainNameExpirationCheck"
              variant="subtle"
              size="sm"
              :icon="$this->monitor->domainCheck?->enabled ? 'check-circle' : 'x-circle'"
              :class="$this->monitor->domainCheck?->enabled ? 'text-emerald-600!' : 'text-gray-300!'"
            />
          </flux:tooltip>
        </div>

        @if(!$this->monitor->domainCheck?->enabled)
          <div class="px-5 py-8 flex flex-col items-center gap-2 text-center">
            <flux:icon.no-symbol class="size-8 text-gray-300" />
            <p class="text-sm text-gray-400">Domain Information check is currently disabled.</p>
            <flux:button wire:click="toggleDomainNameExpirationCheck" variant="ghost" size="sm" icon="check-circle">Enable check</flux:button>
          </div>
        @else
          <dl class="divide-y divide-gray-100">
            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 px-5">
              <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Status</dt>
              <dd class="text-sm sm:mt-0 sm:col-span-2">
                @if($this->monitor->domainCheck?->status->value === 'invalid')
                  <flux:badge size="sm" color="red" icon="x-circle">Invalid</flux:badge>
                @elseif($this->monitor->domainCheck?->status->value === 'not yet checked')
                  <flux:badge size="sm" color="yellow" icon="clock">Pending</flux:badge>
                @else
                  <flux:badge size="sm" color="green" icon="check">Valid</flux:badge>
                @endif
              </dd>
            </div>
            @if($this->monitor->domainCheck?->status->value === 'valid')
              <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 px-5">
                <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Expiration</dt>
                <dd class="text-sm font-medium text-gray-800 sm:mt-0 sm:col-span-2">{{ $this->monitor->domainCheck?->expiration_date?->format("D, F j, Y, g:i a") }}</dd>
              </div>
            @endif
            @if($this->monitor->domainCheck?->status->value === 'invalid')
              <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 px-5">
                <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Failed Reason</dt>
                <dd class="text-sm font-medium text-gray-800 sm:mt-0 sm:col-span-2">{{ $this->monitor->domainCheck?->failure_reason }}</dd>
              </div>
            @endif
            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 px-5">
              <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Cloudflare</dt>
              <dd class="text-sm sm:mt-0 sm:col-span-2">
                @if($this->monitor->domainCheck?->is_on_cloudflare)
                  <flux:badge size="sm" color="green" icon="check">Yes</flux:badge>
                @else
                  <flux:badge size="sm" color="zinc" icon="x-mark">No</flux:badge>
                @endif
              </dd>
            </div>
            @if($this->monitor->domainCheck?->status->value === 'valid')
              <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 px-5">
                <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Nameservers</dt>
                <dd class="text-sm font-medium text-gray-800 sm:mt-0 sm:col-span-2">
                  @if($this->monitor->domainCheck?->nameservers)
                    <ul class="space-y-0.5">
                      @foreach($this->monitor->domainCheck->nameservers as $nameserver)
                        <li>{{ $nameserver }}</li>
                      @endforeach
                    </ul>
                  @else
                    Not fetched
                  @endif
                </dd>
              </div>
            @endif
          </dl>
        @endif
      </flux:card>
      <!-- End Domain Information Card -->

      <!-- Lighthouse Reports Card -->
      <flux:card class="p-0 overflow-hidden divide-y divide-gray-100" id="lighthouse-card">
        <div class="flex items-center justify-between px-5 py-4">
          <div class="flex items-center gap-3 border-l-4 border-yellow-500 pl-3">
            <flux:icon.light-bulb class="size-4 text-yellow-600 shrink-0" />
            <flux:heading level="3" class="text-sm! font-semibold text-gray-800 m-0!">Lighthouse Reports</flux:heading>
          </div>
          <flux:tooltip :content="$this->monitor->lighthouseCheck?->enabled ? 'Disable check' : 'Enable check'">
            <flux:button
              wire:click="toggleLighthouseCheck"
              variant="subtle"
              size="sm"
              :icon="$this->monitor->lighthouseCheck?->enabled ? 'check-circle' : 'x-circle'"
              :class="$this->monitor->lighthouseCheck?->enabled ? 'text-emerald-600!' : 'text-gray-300!'"
            />
          </flux:tooltip>
        </div>

        @php
          $lighthouseCheck = $this->monitor->lighthouseChecks->firstWhere('form_factor', $lighthouseFormFactor) ?? $this->monitor->lighthouseCheck;
        @endphp
        @if(!$this->monitor->lighthouseCheck?->enabled)
          <div class="px-5 py-8 flex flex-col items-center gap-2 text-center">
            <flux:icon.no-symbol class="size-8 text-gray-300" />
            <p class="text-sm text-gray-400">Lighthouse reports are currently disabled.</p>
            <flux:button wire:click="toggleLighthouseCheck" variant="ghost" size="sm" icon="check-circle">Enable check</flux:button>
          </div>
        @else
          <dl class="divide-y divide-gray-100">
            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 px-5">
              <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Status</dt>
              <dd class="text-sm sm:mt-0 sm:col-span-2">
                @if($lighthouseCheck?->status->value === 'invalid')
                  <flux:badge size="sm" color="red" icon="x-circle">Invalid</flux:badge>
                @elseif($lighthouseCheck?->status->value === 'not yet checked')
                  <flux:badge size="sm" color="yellow" icon="clock">Pending</flux:badge>
                @else
                  <flux:badge size="sm" color="green" icon="check">Ok</flux:badge>
                @endif
              </dd>
            </div>
            @if($lighthouseCheck?->status->value === 'valid')
              <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 px-5">
                <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Last Checked</dt>
                <dd class="text-sm font-medium text-gray-800 sm:mt-0 sm:col-span-2">{{ $lighthouseCheck?->last_succeeded_at?->diffForHumans() }}</dd>
              </div>

              {{-- Form factor toggle --}}
              <div class="px-5 py-3 flex items-center justify-between">
                <div class="inline-flex rounded border border-gray-200 overflow-hidden text-xs">
                  <button
                    wire:click="switchLighthouseFormFactor('desktop')"
                    @class([
                      'flex items-center gap-1 px-2.5 py-1 font-medium transition-colors',
                      'bg-accent text-white' => $lighthouseFormFactor === 'desktop',
                      'bg-white text-gray-600 hover:bg-gray-50' => $lighthouseFormFactor !== 'desktop',
                    ])
                  >
                    <flux:icon.computer-desktop class="size-3.5" />
                    Desktop
                  </button>
                  <button
                    wire:click="switchLighthouseFormFactor('mobile')"
                    @class([
                      'flex items-center gap-1 px-2.5 py-1 font-medium transition-colors border-l border-gray-200',
                      'bg-accent text-white' => $lighthouseFormFactor === 'mobile',
                      'bg-white text-gray-600 hover:bg-gray-50' => $lighthouseFormFactor !== 'mobile',
                    ])
                  >
                    <flux:icon.device-phone-mobile class="size-3.5" />
                    Mobile
                  </button>
                </div>
              </div>

              {{-- Lighthouse score tiles --}}
              @php
                $perf = $this->lighthouseStats?->performance_score ?? 0;
                $a11y = $this->lighthouseStats?->accessibility_score ?? 0;
                $bp   = $this->lighthouseStats?->best_practices_score ?? 0;
                $seo  = $this->lighthouseStats?->seo_score ?? 0;
              @endphp
              <div class="px-5 py-4 grid grid-cols-2 gap-2">
                <div @class([
                  'rounded-lg border p-3 text-center',
                  'bg-green-50 border-green-200' => $perf >= 90,
                  'bg-amber-50 border-amber-200' => $perf >= 70 && $perf < 90,
                  'bg-red-50 border-red-200'     => $perf < 70,
                ])>
                  <div class="text-xs font-medium text-gray-500 mb-1">Performance</div>
                  <div @class([
                    'text-xl font-bold',
                    'text-green-700' => $perf >= 90,
                    'text-amber-700' => $perf >= 70 && $perf < 90,
                    'text-red-700'   => $perf < 70,
                  ])>{{ $perf }}</div>
                </div>
                <div @class([
                  'rounded-lg border p-3 text-center',
                  'bg-green-50 border-green-200' => $a11y >= 90,
                  'bg-amber-50 border-amber-200' => $a11y >= 70 && $a11y < 90,
                  'bg-red-50 border-red-200'     => $a11y < 70,
                ])>
                  <div class="text-xs font-medium text-gray-500 mb-1">Accessibility</div>
                  <div @class([
                    'text-xl font-bold',
                    'text-green-700' => $a11y >= 90,
                    'text-amber-700' => $a11y >= 70 && $a11y < 90,
                    'text-red-700'   => $a11y < 70,
                  ])>{{ $a11y }}</div>
                </div>
                <div @class([
                  'rounded-lg border p-3 text-center',
                  'bg-green-50 border-green-200' => $bp >= 90,
                  'bg-amber-50 border-amber-200' => $bp >= 70 && $bp < 90,
                  'bg-red-50 border-red-200'     => $bp < 70,
                ])>
                  <div class="text-xs font-medium text-gray-500 mb-1">Best Practices</div>
                  <div @class([
                    'text-xl font-bold',
                    'text-green-700' => $bp >= 90,
                    'text-amber-700' => $bp >= 70 && $bp < 90,
                    'text-red-700'   => $bp < 70,
                  ])>{{ $bp }}</div>
                </div>
                <div @class([
                  'rounded-lg border p-3 text-center',
                  'bg-green-50 border-green-200' => $seo >= 90,
                  'bg-amber-50 border-amber-200' => $seo >= 70 && $seo < 90,
                  'bg-red-50 border-red-200'     => $seo < 70,
                ])>
                  <div class="text-xs font-medium text-gray-500 mb-1">SEO</div>
                  <div @class([
                    'text-xl font-bold',
                    'text-green-700' => $seo >= 90,
                    'text-amber-700' => $seo >= 70 && $seo < 90,
                    'text-red-700'   => $seo < 70,
                  ])>{{ $seo }}</div>
                </div>
              </div>

              <div class="px-5 py-4">
                <flux:button :href="route('monitors.lighthouse', $this->monitor->id)" variant="subtle" size="sm" icon="arrow-top-right-on-square">
                  View Full Reports
                </flux:button>
              </div>
            @endif
            @if($lighthouseCheck?->status->value === 'invalid')
              <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 px-5">
                <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Last Checked</dt>
                <dd class="text-sm font-medium text-gray-800 sm:mt-0 sm:col-span-2">{{ $lighthouseCheck?->last_failed_at }}</dd>
              </div>
              <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 px-5">
                <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide">Last Error</dt>
                <dd class="text-sm font-medium text-gray-800 sm:mt-0 sm:col-span-2">{{ $lighthouseCheck?->failure_reason }}</dd>
              </div>
            @endif
          </dl>
        @endif
      </flux:card>
      <!-- End Lighthouse Reports Card -->

    </div>
    <!-- End Right Column -->

  </div>
  <!-- End Two-Column Layout -->

</div>
