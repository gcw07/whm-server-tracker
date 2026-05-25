<div>
  <div class="pb-5">
    <h3 class="text-2xl leading-6 font-medium text-gray-900">
      Reports
    </h3>
  </div>

  <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <flux:card class="flex flex-col gap-4">
      <div class="flex items-center gap-3">
        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-cyan-100 text-cyan-600">
          <flux:icon.code-bracket class="size-5" />
        </div>
        <div>
          <h3 class="text-base font-semibold text-gray-900">WP Updates</h3>
          <p class="text-sm text-gray-500">Plugin, theme &amp; WordPress version status</p>
        </div>
      </div>
      <flux:button :href="route('reports.wp-updates')" icon:trailing="arrow-right" variant="ghost" class="self-start">
        View Report
      </flux:button>
    </flux:card>

    <flux:card class="flex flex-col gap-4">
      <div class="flex items-center gap-3">
        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-blue-100 text-blue-600">
          <flux:icon.lock-closed class="size-5" />
        </div>
        <div>
          <h3 class="text-base font-semibold text-gray-900">SSL Certificates</h3>
          <p class="text-sm text-gray-500">Certificate expiry status across monitors &amp; accounts</p>
        </div>
      </div>
      <flux:button :href="route('reports.ssl-certificates')" icon:trailing="arrow-right" variant="ghost" class="self-start">
        View Report
      </flux:button>
    </flux:card>

    <flux:card class="flex flex-col gap-4">
      <div class="flex items-center gap-3">
        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-purple-100 text-purple-600">
          <flux:icon.globe-alt class="size-5" />
        </div>
        <div>
          <h3 class="text-base font-semibold text-gray-900">Domain Expiry</h3>
          <p class="text-sm text-gray-500">Domain registration expiry across all monitors</p>
        </div>
      </div>
      <flux:button :href="route('reports.domain-expiry')" icon:trailing="arrow-right" variant="ghost" class="self-start">
        View Report
      </flux:button>
    </flux:card>

    <flux:card class="flex flex-col gap-4">
      <div class="flex items-center gap-3">
        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-green-100 text-green-600">
          <flux:icon.code-bracket-square class="size-5" />
        </div>
        <div>
          <h3 class="text-base font-semibold text-gray-900">PHP Versions</h3>
          <p class="text-sm text-gray-500">PHP version breakdown &amp; end-of-life status</p>
        </div>
      </div>
      <flux:button :href="route('reports.php-versions')" icon:trailing="arrow-right" variant="ghost" class="self-start">
        View Report
      </flux:button>
    </flux:card>

    <flux:card class="flex flex-col gap-4">
      <div class="flex items-center gap-3">
        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-indigo-100 text-indigo-600">
          <flux:icon.signal class="size-5" />
        </div>
        <div>
          <h3 class="text-base font-semibold text-gray-900">Uptime Summary</h3>
          <p class="text-sm text-gray-500">7 &amp; 30-day uptime percentages across all monitors</p>
        </div>
      </div>
      <flux:button :href="route('reports.uptime-summary')" icon:trailing="arrow-right" variant="ghost" class="self-start">
        View Report
      </flux:button>
    </flux:card>

    <flux:card class="flex flex-col gap-4">
      <div class="flex items-center gap-3">
        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-violet-100 text-violet-600">
          <flux:icon.circle-stack class="size-5" />
        </div>
        <div>
          <h3 class="text-base font-semibold text-gray-900">Disk Usage</h3>
          <p class="text-sm text-gray-500">Server &amp; account disk usage and capacity</p>
        </div>
      </div>
      <flux:button :href="route('reports.disk-usage')" icon:trailing="arrow-right" variant="ghost" class="self-start">
        View Report
      </flux:button>
    </flux:card>

    <flux:card class="flex flex-col gap-4">
      <div class="flex items-center gap-3">
        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-yellow-100 text-yellow-600">
          <flux:icon.light-bulb class="size-5" />
        </div>
        <div>
          <h3 class="text-base font-semibold text-gray-900">Lighthouse Performance</h3>
          <p class="text-sm text-gray-500">Latest performance, accessibility, SEO &amp; best practices scores</p>
        </div>
      </div>
      <flux:button :href="route('reports.lighthouse-performance')" icon:trailing="arrow-right" variant="ghost" class="self-start">
        View Report
      </flux:button>
    </flux:card>

    <flux:card class="flex flex-col gap-4">
      <div class="flex items-center gap-3">
        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-rose-100 text-rose-600">
          <flux:icon.puzzle-piece class="size-5" />
        </div>
        <div>
          <h3 class="text-base font-semibold text-gray-900">WP Plugins</h3>
          <p class="text-sm text-gray-500">WordPress plugins with updates available across all sites</p>
        </div>
      </div>
      <flux:button :href="route('reports.wp-plugins')" icon:trailing="arrow-right" variant="ghost" class="self-start">
        View Report
      </flux:button>
    </flux:card>

    <flux:card class="flex flex-col gap-4">
      <div class="flex items-center gap-3">
        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-red-100 text-red-600">
          <flux:icon.envelope class="size-5" />
        </div>
        <div>
          <h3 class="text-base font-semibold text-gray-900">Blacklisted Sites</h3>
          <p class="text-sm text-gray-500">Email blacklist status across all monitored sites</p>
        </div>
      </div>
      <flux:button :href="route('reports.blacklisted-sites')" icon:trailing="arrow-right" variant="ghost" class="self-start">
        View Report
      </flux:button>
    </flux:card>

    <flux:card class="flex flex-col gap-4">
      <div class="flex items-center gap-3">
        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-zinc-100 text-zinc-600">
          <flux:icon.no-symbol class="size-5" />
        </div>
        <div>
          <h3 class="text-base font-semibold text-gray-900">Suspended Accounts</h3>
          <p class="text-sm text-gray-500">All suspended hosting accounts across all servers</p>
        </div>
      </div>
      <flux:button :href="route('reports.suspended-accounts')" icon:trailing="arrow-right" variant="ghost" class="self-start">
        View Report
      </flux:button>
    </flux:card>

    <flux:card class="flex flex-col gap-4">
      <div class="flex items-center gap-3">
        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-orange-100 text-orange-600">
          <flux:icon.bolt class="size-5" />
        </div>
        <div>
          <h3 class="text-base font-semibold text-gray-900">Cloudflare Traffic</h3>
          <p class="text-sm text-gray-500">30-day visitors, requests &amp; bandwidth per site</p>
        </div>
      </div>
      <flux:button :href="route('reports.cloudflare-traffic')" icon:trailing="arrow-right" variant="ghost" class="self-start">
        View Report
      </flux:button>
    </flux:card>
  </div>
</div>
