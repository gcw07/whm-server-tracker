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
  </div>
</div>
