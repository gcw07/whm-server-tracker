<div>
  <!-- Page Header -->
  <div class="pb-5 border-b border-gray-200 md:flex md:items-center md:justify-between">
    <div class="flex-1 min-w-0">
      <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('dashboard')">Home</flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('accounts.index')">Accounts</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>Details</flux:breadcrumbs.item>
      </flux:breadcrumbs>

      <h3 class="mt-2 text-2xl leading-6 font-medium text-gray-900">
        {{ $account->domain }}
      </h3>
    </div>

    <div class="flex mt-3 md:mt-0 md:ml-4 gap-2">
      <flux:button :href="$account->domain_url" icon="arrow-top-right-on-square" target="_blank">View</flux:button>
    </div>
  </div>
  <!-- / End Page Header -->

  <div class="mt-6">
    <!-- Begin content -->

    <div class="sm:block">
      <dl class="mt-5 grid grid-cols-1 rounded-lg bg-white overflow-hidden shadow lg:grid-cols-3">
        <div class="lg:border-r lg:border-gray-200">
          <dt class="bg-gray-50 border-b border-gray-200 text-lg p-5 font-normal text-gray-900 flex items-center">
            <div class="bg-sky-500 rounded-md p-1 mr-2">
              <flux:icon.information-circle variant="solid" class="text-white" />
            </div>
            Details
          </dt>
          <dl class="sm:divide-y sm:divide-gray-200 px-5 sm:pt-4 sm:pb-1">
            <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
              <dt class="text-sm font-medium text-gray-400">
                Server
              </dt>
              <dd class="mt-1 text-sm font-semibold text-gray-600 sm:mt-0 sm:col-span-2">
                <a href="{{ route('servers.show', $account->server->id) }}" class="text-gray-500 hover:text-gray-900">
                  {{ $account->server->name }}
                </a>
              </dd>
            </div>
            <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
              <dt class="text-sm font-medium text-gray-400">
                Username
              </dt>
              <dd class="mt-1 text-sm font-semibold text-gray-600 sm:mt-0 sm:col-span-2">
                {{ $account->user }}
              </dd>
            </div>
            <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
              <dt class="text-sm font-medium text-gray-400">
                IP
              </dt>
              <dd class="mt-1 text-sm font-semibold text-gray-600 sm:mt-0 sm:col-span-2">
                {{ $account->ip }}
              </dd>
            </div>
            <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
              <dt class="text-sm font-medium text-gray-400">
                Setup Date
              </dt>
              <dd class="mt-1 text-sm font-semibold text-gray-600 sm:mt-0 sm:col-span-2">
                {{ $account->setup_date->format("D, F j, Y, g:i a") }}
              </dd>
            </div>
            <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
              <dt class="text-sm font-medium text-gray-400">
                WordPress
              </dt>
              <dd class="mt-1 text-sm font-semibold text-gray-600 sm:mt-0 sm:col-span-2">
                @if($account->wordpress_version)
                  {{ $account->wordpress_version }}
                @else
                  WP not detected
                @endif
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
          <dl class="sm:divide-y sm:divide-gray-200 px-5 sm:pt-4 sm:pb-1">
            <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
              <dt class="text-sm font-medium text-gray-400">
                Usage
              </dt>
              <dd class="mt-1 text-sm font-semibold text-gray-600 sm:mt-0 sm:col-span-2">
                @if($account->formatted_disk_usage === 'Unknown')
                  <span class="text-gray-900 font-medium">&mdash;</span>
                @else
                  <span class="text-gray-900 font-medium">{{ $account->formatted_disk_usage }}</span>
                @endif
              </dd>
            </div>
            <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
              <dt class="text-sm font-medium text-gray-400">
                Used
              </dt>
              <dd class="mt-1 text-sm font-semibold text-gray-600 sm:mt-0 sm:col-span-2">
                {{ $account->disk_used }}
              </dd>
            </div>
            <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
              <dt class="text-sm font-medium text-gray-400">
                Total
              </dt>
              <dd class="mt-1 text-sm font-semibold text-gray-600 sm:mt-0 sm:col-span-2">
                {{ $account->disk_limit }}
              </dd>
            </div>
            <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
              <dt class="text-sm font-medium text-gray-400">
                Plan
              </dt>
              <dd class="mt-1 text-sm font-semibold text-gray-600 sm:mt-0 sm:col-span-2">
                {{ $account->plan }}
              </dd>
            </div>
          </dl>
        </div>

        <div>
          <dt class="bg-gray-50 border-b border-gray-200 text-lg p-5 font-normal text-gray-900 flex items-center">
            <div class="bg-sky-500 rounded-md p-1 mr-2">
              <flux:icon.computer-desktop variant="solid" class="text-white" />
            </div>
            Other
          </dt>
          <dl class="sm:divide-y sm:divide-gray-200 px-5 sm:pt-4 sm:pb-1">
            <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
              <dt class="text-sm font-medium text-gray-400">
                Backups
              </dt>
              <dd class="mt-1 text-sm font-semibold text-gray-600 sm:mt-0 sm:col-span-2">
                @if($account->backups_enabled)
                  Yes
                @else
                  No
                @endif
              </dd>
            </div>
            <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
              <dt class="text-sm font-medium text-gray-400">
                Suspended
              </dt>
              <dd class="mt-1 text-sm font-semibold text-gray-600 sm:mt-0 sm:col-span-2">
                @if($account->suspended)
                  Yes
                @else
                  No
                @endif
              </dd>
            </div>
            <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
              <dt class="text-sm font-medium text-gray-400">
                Reason
              </dt>
              <dd class="mt-1 text-sm font-semibold text-gray-600 sm:mt-0 sm:col-span-2">
                @if($account->suspended)
                  {{ $account->suspend_reason }}
                @else
                  N/A
                @endif
              </dd>
            </div>
            <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
              <dt class="text-sm font-medium text-gray-400">
                Suspended On
              </dt>
              <dd class="mt-1 text-sm font-semibold text-gray-600 sm:mt-0 sm:col-span-2">
                @if($account->suspended)
                  {{ $account->suspend_time->format("D, F j, Y, g:i a") }}
                @else
                  N/A
                @endif
              </dd>
            </div>

          </dl>
        </div>
      </dl>
    </div>

    <!-- /End Content -->
  </div>

</div>
