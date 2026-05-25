<div>
  <div class="pb-5">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
      <flux:link :href="route('reports.index')" variant="subtle">Reports</flux:link>
      <flux:icon.chevron-right class="size-4" />
      <span>Lighthouse Performance</span>
    </div>
    <div class="flex items-start justify-between gap-4">
      <div>
        <h3 class="text-2xl leading-6 font-medium text-gray-900">
          Lighthouse Performance
        </h3>
        <p class="mt-1 text-sm text-gray-500">Latest Lighthouse scores for all audited sites. Sorted by lowest score first.</p>
      </div>

      {{-- Form Factor Toggle --}}
      <div class="flex shrink-0 items-center rounded-lg border border-gray-200 bg-white p-1 gap-1">
        <button
          wire:click="setFormFactor('desktop')"
          @class([
            'flex items-center gap-1.5 rounded-md px-3 py-1.5 text-sm font-medium transition-colors',
            'bg-accent text-white' => $formFactor === 'desktop',
            'text-gray-600 hover:bg-gray-50' => $formFactor !== 'desktop',
          ])
        >
          <flux:icon.computer-desktop class="size-3.5" />
          Desktop
        </button>
        <button
          wire:click="setFormFactor('mobile')"
          @class([
            'flex items-center gap-1.5 rounded-md px-3 py-1.5 text-sm font-medium transition-colors',
            'bg-accent text-white' => $formFactor === 'mobile',
            'text-gray-600 hover:bg-gray-50' => $formFactor !== 'mobile',
          ])
        >
          <flux:icon.device-phone-mobile class="size-3.5" />
          Mobile
        </button>
      </div>
    </div>
  </div>

  <flux:card class="p-0 overflow-hidden bg-gray-50 mt-6">
    <flux:table :paginate="$this->audits" pagination:scroll-to>
      <flux:table.columns>
        <flux:table.column class="px-6! bg-gray-50 font-medium text-gray-500! text-xs tracking-wide" sortable :sorted="$lhSortBy === 'monitors.url'" :direction="$lhSortDirection" wire:click="sort('monitors.url')">SITE</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide" sortable :sorted="$lhSortBy === 'performance_score'" :direction="$lhSortDirection" wire:click="sort('performance_score')">PERFORMANCE</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide" sortable :sorted="$lhSortBy === 'accessibility_score'" :direction="$lhSortDirection" wire:click="sort('accessibility_score')">ACCESSIBILITY</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide" sortable :sorted="$lhSortBy === 'best_practices_score'" :direction="$lhSortDirection" wire:click="sort('best_practices_score')">BEST PRACTICES</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide" sortable :sorted="$lhSortBy === 'seo_score'" :direction="$lhSortDirection" wire:click="sort('seo_score')">SEO</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">AUDITED</flux:table.column>
      </flux:table.columns>

      <flux:table.rows>
        @forelse ($this->audits as $audit)
          <flux:table.row :key="$audit->id" @class(['bg-gray-50' => $loop->even, 'bg-white' => $loop->odd])>
            <flux:table.cell class="px-6! py-5!">
              <flux:link variant="subtle" :href="route('monitors.lighthouse', $audit->monitor_id)">
                {{ preg_replace("(^https?://)", "", $audit->monitor->url) }}
              </flux:link>
            </flux:table.cell>

            <flux:table.cell class="whitespace-nowrap">
              @php $s = $audit->performance_score; @endphp
              <flux:badge size="sm" :color="$s >= 90 ? 'green' : ($s >= 70 ? 'amber' : 'red')">{{ $s }}</flux:badge>
            </flux:table.cell>

            <flux:table.cell class="whitespace-nowrap">
              @php $s = $audit->accessibility_score; @endphp
              <flux:badge size="sm" :color="$s >= 90 ? 'green' : ($s >= 70 ? 'amber' : 'red')">{{ $s }}</flux:badge>
            </flux:table.cell>

            <flux:table.cell class="whitespace-nowrap">
              @php $s = $audit->best_practices_score; @endphp
              <flux:badge size="sm" :color="$s >= 90 ? 'green' : ($s >= 70 ? 'amber' : 'red')">{{ $s }}</flux:badge>
            </flux:table.cell>

            <flux:table.cell class="whitespace-nowrap">
              @php $s = $audit->seo_score; @endphp
              <flux:badge size="sm" :color="$s >= 90 ? 'green' : ($s >= 70 ? 'amber' : 'red')">{{ $s }}</flux:badge>
            </flux:table.cell>

            <flux:table.cell class="whitespace-nowrap text-sm text-gray-500">
              {{ $audit->created_at->diffForHumans() }}
            </flux:table.cell>
          </flux:table.row>
        @empty
          <flux:table.row>
            <flux:table.cell colspan="6" class="py-8 whitespace-nowrap font-semibold text-zinc-700">
              <div class="text-center">
                <div class="flex items-center justify-center">
                  <flux:icon.magnifying-glass class="size-12" />
                </div>
                <p class="text-lg mt-6">No Lighthouse audits found for {{ $formFactor }}.</p>
              </div>
            </flux:table.cell>
          </flux:table.row>
        @endforelse
      </flux:table.rows>
    </flux:table>
  </flux:card>
</div>
