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
        {{ $domainUrl }}
      </h3>
    </div>

    <div class="flex mt-3 md:mt-0 md:ml-4 gap-2">
      <flux:button :href="$monitor->url" icon="arrow-top-right-on-square" target="_blank">View</flux:button>

      <flux:dropdown position="bottom" align="end">
        <flux:button icon="arrow-path" icon:trailing="chevron-down">Refresh</flux:button>

        <flux:menu>
          <flux:menu.item wire:click="refreshCertificateCheck" icon="lock-closed">SSL Certificate</flux:menu.item>
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

  <div class="mt-6">
    <!-- Begin content -->

    <!-- Details Card -->
    <flux:card class="divide-y divide-gray-200 p-0 overflow-auto">
      <flux:heading level="3" class="text-lg! bg-zinc-50 px-6 py-5">Details</flux:heading>
      <div class="px-4 py-5 sm:p-0">
        <dl class="sm:divide-y sm:divide-gray-200">
          @if($this->account->suspended)
            <div class="py-4 flex justify-center items-center text-base font-medium text-red-700 bg-red-50">
              <flux:icon.exclamation-triangle variant="solid" />
              This account is suspended
            </div>
          @endif
          <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Server</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              <a href="{{ route('servers.show', $this->account->server->id) }}" class="group inline-flex space-x-2 truncate text-sm">
                <p class="text-gray-500 truncate font-semibold group-hover:text-gray-900">
                  {{ $this->account->server->name }}
                </p>
              </a>
              @if($accountsCount > 1)
                <span class="ml-5 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-200 text-red-800 capitalize">
                  Found on multiple servers
                </span>
              @endif
            </dd>
          </div>
          <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Disk Usage</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ $this->account->formatted_disk_usage }}
            </dd>
          </div>
          <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">WordPress</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              @if($this->account->wordpress_version)
                {{ $this->account->wordpress_version }}
              @else
                WP not detected
              @endif
            </dd>
          </div>
        </dl>
      </div>
    </flux:card>
    <!-- End Details Card -->

    <!-- Uptime Checks Card -->
    <flux:card class="mt-5 divide-y divide-gray-200 p-0 overflow-auto">
      <flux:heading level="3" class="text-lg! bg-zinc-50 px-6 py-5 flex items-center justify-between">
        <div class="flex items-center gap-2">
          <flux:icon.check-badge />
          Uptime Checks
        </div>
        @if($monitor->uptime_check_enabled)
          <flux:button wire:click="toggleUptimeCheck" size="sm" variant="primary" color="emerald" icon="check">On</flux:button>
        @else
          <flux:button wire:click="toggleUptimeCheck" size="sm" variant="primary" color="rose" icon="x-circle">Off</flux:button>
        @endif
      </flux:heading>
      <div class="px-4 py-5 sm:p-0">
        @if(!$monitor->uptime_check_enabled)
          <div class="bg-yellow-100 text-center  p-3">Uptime check is disabled</div>
          <div class="px-4 py-5 sm:p-0 opacity-20">
            <dl class="sm:divide-y sm:divide-gray-200">
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Current Status</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                  N/A
                </dd>
              </div>
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Last Checked</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">N/A</dd>
              </div>
            </dl>
          </div>
        @else
          <div class="px-4 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-200">
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Current Status</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                  @if($monitor->uptime_status === 'down')
                    Down
                  @elseif($monitor->uptime_status === 'not yet checked')
                    Pending
                  @else
                    Up
                  @endif
                </dd>
              </div>
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Last Checked</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $monitor->uptime_last_check_date?->diffForHumans() }}</dd>
              </div>
              <div class="py-4 sm:py-5 sm:px-6">
                <div class="flex w-full items-center justify-between">
                  <div>
                    <dt class="text-xs font-normal text-gray-500">Today</dt>
                    <dd class="mt-1 flex items-baseline justify-between text-2xl font-semibold text-sky-600 md:block lg:flex">
                      {{ $this->monitor->uptime_for_today }}%
                    </dd>
                  </div>
                  <div>
                    <dt class="text-xs font-normal text-gray-500">Last 7 Days</dt>
                    <dd class="mt-1 flex items-baseline justify-between text-2xl font-semibold text-sky-600 md:block lg:flex">
                      {{ $this->monitor->uptime_for_last_seven_days }}%
                    </dd>
                  </div>
                  <div>
                    <dt class="text-xs font-normal text-gray-500">Last 30 Days</dt>
                    <dd class="mt-1 flex items-baseline justify-between text-2xl font-semibold text-sky-600 md:block lg:flex">
                      {{ $this->monitor->uptime_for_last_thirty_days }}%
                    </dd>
                  </div>
                </div>
              </div>
              @if($monitor->uptime_status === 'down')
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">Failure Reason</dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $monitor->uptime_check_failure_reason }}</dd>
                </div>
              @endif
            </dl>
          </div>
        @endif
      </div>
    </flux:card>
    <!-- End Uptime Checks Card -->

    <!-- SSL Certificate Checks Card -->
    <flux:card class="mt-5 divide-y divide-gray-200 p-0 overflow-auto">
      <flux:heading level="3" class="text-lg! bg-zinc-50 px-6 py-5 flex items-center justify-between">
        <div class="flex items-center gap-2">
          <flux:icon.lock-closed />
          SSL Certificate
        </div>
        @if($monitor->certificate_check_enabled)
          <flux:button wire:click="toggleCertificateCheck" size="sm" variant="primary" color="emerald" icon="check">On</flux:button>
        @else
          <flux:button wire:click="toggleCertificateCheck" size="sm" variant="primary" color="rose" icon="x-circle">Off</flux:button>
        @endif
      </flux:heading>
      <div class="px-4 py-5 sm:p-0">
        @if(!$monitor->certificate_check_enabled)
          <div class="bg-yellow-100 text-center  p-3">SSL Certificate check is disabled</div>
          <div class="px-4 py-5 sm:p-0 opacity-20">
            <dl class="sm:divide-y sm:divide-gray-200">
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Current Status</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                  N/A
                </dd>
              </div>
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Expiration Date</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">N/A</dd>
              </div>
            </dl>
          </div>
        @else
          <div class="px-4 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-200">
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Current Status</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                  @if($monitor->certificate_status === 'invalid')
                    Invalid
                  @elseif($monitor->certificate_status === 'not yet checked')
                    Pending
                  @else
                    Ok
                  @endif
                </dd>
              </div>
              @if($monitor->certificate_status === 'valid')
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">Expiration Date</dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $monitor->certificate_expiration_date->format("D, F j, Y, g:i a") }}</dd>
                </div>
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">Issued By</dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $monitor->certificate_issuer }}</dd>
                </div>
              @endif
              @if($monitor->certificate_status === 'invalid')
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">Failure Reason</dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $monitor->certificate_check_failure_reason }}</dd>
                </div>
              @endif
            </dl>
          </div>
        @endif
      </div>
    </flux:card>
    <!-- End SSL Certificate Checks Card -->

    <!-- Email Blacklist Checks Card -->
    <flux:card class="mt-5 divide-y divide-gray-200 p-0 overflow-auto">
      <flux:heading level="3" class="text-lg! bg-zinc-50 px-6 py-5 flex items-center justify-between">
        <div class="flex items-center gap-2">
          <flux:icon.envelope />
          Email Blacklist
        </div>
        @if($monitor->blacklist_check_enabled)
          <flux:button wire:click="toggleBlacklistCheck" size="sm" variant="primary" color="emerald" icon="check">On</flux:button>
        @else
          <flux:button wire:click="toggleBlacklistCheck" size="sm" variant="primary" color="rose" icon="x-circle">Off</flux:button>
        @endif
      </flux:heading>
      <div class="px-4 py-5 sm:p-0">
        @if(!$monitor->blacklist_check_enabled)
          <div class="bg-yellow-100 text-center  p-3">Email Blacklist check is disabled</div>
          <div class="px-4 py-5 sm:p-0 opacity-20">
            <dl class="sm:divide-y sm:divide-gray-200">
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Current Status</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                  N/A
                </dd>
              </div>
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">List</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">N/A</dd>
              </div>
            </dl>
          </div>
        @else
          <div class="px-4 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-200">
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Current Status</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                  @if($monitor->blacklist_status === 'invalid')
                    Found
                  @elseif($monitor->blacklist_status === 'not yet checked')
                    Pending
                  @else
                    Ok
                  @endif
                </dd>
              </div>
              @if($monitor->blacklist_status === 'valid')
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">List</dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">Not found on any blacklist</dd>
                </div>
              @endif
              @if($monitor->blacklist_status === 'invalid')
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">List</dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    {!! nl2br($monitor->blacklist_check_failure_reason) !!}
                  </dd>
                </div>
              @endif
            </dl>
          </div>
        @endif
      </div>
    </flux:card>
    <!-- End Email Blacklist Checks Card -->

    <!-- Domain Information Checks Card -->
    <flux:card class="mt-5 divide-y divide-gray-200 p-0 overflow-auto">
      <flux:heading level="3" class="text-lg! bg-zinc-50 px-6 py-5 flex items-center justify-between">
        <div class="flex items-center gap-2">
          <flux:icon.identification />
          Domain Information
        </div>
        @if($monitor->domain_name_check_enabled)
          <flux:button wire:click="toggleDomainNameExpirationCheck" size="sm" variant="primary" color="emerald" icon="check">On</flux:button>
        @else
          <flux:button wire:click="toggleDomainNameExpirationCheck" size="sm" variant="primary" color="rose" icon="x-circle">Off</flux:button>
        @endif
      </flux:heading>
      <div class="px-4 py-5 sm:p-0">
        @if(!$monitor->domain_name_check_enabled)
          <div class="bg-yellow-100 text-center  p-3">Domain Information checks are disabled</div>
          <div class="px-4 py-5 sm:p-0 opacity-20">
            <dl class="sm:divide-y sm:divide-gray-200">
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Current Status</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                  N/A
                </dd>
              </div>
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Expiration Date</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">N/A</dd>
              </div>
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">On Cloudflare</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                  N/A
                </dd>
              </div>
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Nameservers</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">N/A</dd>
              </div>
            </dl>
          </div>
        @else
          <div class="px-4 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-200">
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Current Status</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                  @if($monitor->domain_name_status === 'invalid')
                    Invalid
                  @elseif($monitor->domain_name_status === 'not yet checked')
                    Pending
                  @else
                    Valid
                  @endif
                </dd>
              </div>
              @if($monitor->domain_name_status === 'valid')
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">Expiration Date</dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $monitor->domain_name_expiration_date?->format("D, F j, Y, g:i a") }}</dd>
                </div>
              @endif
              @if($monitor->domain_name_status === 'invalid')
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">Failed Reason</dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $monitor->domain_name_check_failure_reason }}</dd>
                </div>
              @endif
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">On Cloudflare</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                  @if($monitor->is_on_cloudflare)
                    Yes
                  @else
                    No
                  @endif
                </dd>
              </div>
              @if($monitor->domain_name_status === 'valid')
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">Nameservers</dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    @if($monitor->nameservers)
                      @foreach($monitor->nameservers as $nameserver)
                        {{ $nameserver }}<br>
                      @endforeach
                    @else
                      Not fetched
                    @endif
                  </dd>
                </div>
              @endif
            </dl>
          </div>
        @endif
      </div>
    </flux:card>
    <!-- End Domain Information Checks Card -->

    <!--Lighthouse Reports Checks Card -->
    <flux:card class="mt-5 divide-y divide-gray-200 p-0 overflow-auto">
      <flux:heading level="3" class="text-lg! bg-zinc-50 px-6 py-5 flex items-center justify-between">
        <div class="flex items-center gap-2">
          <flux:icon.light-bulb />
          Lighthouse Reports
        </div>
        @if($monitor->lighthouse_check_enabled)
          <flux:button wire:click="toggleLighthouseCheck" size="sm" variant="primary" color="emerald" icon="check">On</flux:button>
        @else
          <flux:button wire:click="toggleLighthouseCheck" size="sm" variant="primary" color="rose" icon="x-circle">Off</flux:button>
        @endif
      </flux:heading>
      <div class="px-4 py-5 sm:p-0">
        @if(!$monitor->lighthouse_check_enabled)
          <div class="bg-yellow-100 text-center  p-3">Lighthouse reports are disabled</div>
          <div class="px-4 py-5 sm:p-0 opacity-20">
            <dl class="sm:divide-y sm:divide-gray-200">
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Current Status</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                  N/A
                </dd>
              </div>
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Last Checked</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">N/A</dd>
              </div>
            </dl>
          </div>
        @else
          <div class="px-4 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-200">
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Current Status</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                  @if($monitor->lighthouse_status === 'invalid')
                    Invalid
                  @elseif($monitor->lighthouse_status === 'not yet checked')
                    Pending
                  @else
                    Ok
                  @endif
                </dd>
              </div>
              @if($monitor->lighthouse_status === 'valid')
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">Last Checked</dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $monitor->lighthouse_update_last_succeeded_at?->diffForHumans() }}</dd>
                </div>
                <div class="py-4 sm:py-5 sm:px-6">
                  <div class="flex w-full items-center justify-between">
                    <div>
                      <dt class="text-xs font-normal text-gray-500">Performance</dt>
                      <dd class="mt-1 flex items-baseline justify-between text-2xl font-semibold text-sky-600 md:block lg:flex">
                        {{ $this->lighthouseStats->performance_score }}
                      </dd>
                    </div>
                    <div>
                      <dt class="text-xs font-normal text-gray-500">Accessibility</dt>
                      <dd class="mt-1 flex items-baseline justify-between text-2xl font-semibold text-sky-600 md:block lg:flex">
                        {{ $this->lighthouseStats->accessibility_score }}
                      </dd>
                    </div>
                    <div>
                      <dt class="text-xs font-normal text-gray-500">Best Practices</dt>
                      <dd class="mt-1 flex items-baseline justify-between text-2xl font-semibold text-sky-600 md:block lg:flex">
                        {{ $this->lighthouseStats->best_practices_score }}
                      </dd>
                    </div>
                    <div>
                      <dt class="text-xs font-normal text-gray-500">SEO</dt>
                      <dd class="mt-1 flex items-baseline justify-between text-2xl font-semibold text-sky-600 md:block lg:flex">
                        {{ $this->lighthouseStats->seo_score }}
                      </dd>
                    </div>
                  </div>
                </div>
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <a href="{{ route('monitors.lighthouse', $monitor->id) }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">View Full Reports</a>
                </div>
              @endif
              @if($monitor->lighthouse_status === 'invalid')
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">Last Checked</dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $monitor->lighthouse_update_last_failed_at }}</dd>
                </div>
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">Last Error Message</dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $monitor->lighthouse_check_failure_reason }}</dd>
                </div>
              @endif
            </dl>
          </div>
        @endif
      </div>
    </flux:card>
    <!-- End Lighthouse Reports Checks Card -->

    <!-- /End Content -->
  </div>

</div>
