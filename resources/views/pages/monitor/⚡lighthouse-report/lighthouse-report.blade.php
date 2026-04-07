<div>
  <!-- Page Header -->
  <div class="pb-5 border-b border-gray-200 md:flex md:items-center md:justify-between">
    <div class="flex-1 min-w-0">
      <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('dashboard')">Home</flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('monitors.index')">Monitors</flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('monitors.show', $monitor->id)">Details</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>Lighthouse Reports</flux:breadcrumbs.item>
      </flux:breadcrumbs>

      <h3 class="mt-2 text-2xl leading-6 font-medium text-gray-900">
        {{ $domainUrl }}
      </h3>
    </div>
  </div>
  <!-- / End Page Header -->

  <div class="mt-6 flex gap-6 items-start">

    <!-- Audit History Sidebar -->
    <div class="w-56 shrink-0">
      <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="bg-gray-50 px-4 py-4 border-b border-gray-200">
          <h3 class="text-sm font-semibold text-gray-700">Recent Reports</h3>
        </div>
        <ul class="divide-y divide-gray-100">
          @forelse($audits as $audit)
            <li
              wire:click.prevent="changeSelected({{ $audit->id }})"
              @class([
                'px-4 py-3 cursor-pointer text-sm transition-colors',
                'bg-cyan-50 text-cyan-700 font-semibold' => $audit->id === $selectedAudit?->id,
                'text-gray-600 hover:bg-gray-50 font-medium' => $audit->id !== $selectedAudit?->id,
              ])
            >
              {{ $audit->created_at->format('M j, Y') }}
            </li>
          @empty
            <li class="px-4 py-3 text-sm text-gray-500">No audits yet.</li>
          @endforelse
        </ul>
      </div>
    </div>

    <!-- Audit Detail -->
    <div class="flex-1 min-w-0 space-y-6">

      <!-- Form Factor Toggle -->
      <div class="flex">
        <div class="inline-flex rounded-lg shadow-xs border border-gray-200 overflow-hidden">
          <button
            wire:click="switchFormFactor('desktop')"
            @class([
              'flex items-center gap-1.5 px-4 py-2 text-sm font-medium transition-colors',
              'bg-accent text-white' => $formFactor === 'desktop',
              'bg-white text-gray-600 hover:bg-gray-50' => $formFactor !== 'desktop',
            ])
          >
            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M2 4.25A2.25 2.25 0 0 1 4.25 2h11.5A2.25 2.25 0 0 1 18 4.25v8.5A2.25 2.25 0 0 1 15.75 15h-3.105a3.501 3.501 0 0 0 1.1 1.677A.75.75 0 0 1 13.26 18H6.74a.75.75 0 0 1-.484-1.323A3.501 3.501 0 0 0 7.355 15H4.25A2.25 2.25 0 0 1 2 12.75v-8.5Z" clip-rule="evenodd" />
            </svg>
            Desktop
          </button>
          <button
            wire:click="switchFormFactor('mobile')"
            @class([
              'flex items-center gap-1.5 px-4 py-2 text-sm font-medium transition-colors border-l border-gray-200',
              'bg-accent text-white' => $formFactor === 'mobile',
              'bg-white text-gray-600 hover:bg-gray-50' => $formFactor !== 'mobile',
            ])
          >
            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path d="M8 16.25a.75.75 0 0 1 .75-.75h2.5a.75.75 0 0 1 0 1.5h-2.5a.75.75 0 0 1-.75-.75Z" />
              <path fill-rule="evenodd" d="M4 4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4Zm6 11.75a.75.75 0 0 0 0 1.5h.008a.75.75 0 0 0 0-1.5H10Z" clip-rule="evenodd" />
            </svg>
            Mobile
          </button>
        </div>
      </div>

    @if($selectedAudit)

        <!-- Category Scores -->
        <div class="bg-white shadow rounded-lg p-6">
          <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-semibold text-gray-900">Category Scores</h3>
            <div class="flex items-center gap-3">
              <span class="text-sm text-gray-500">
                {{ $selectedAudit->created_at->format('l, F j, Y') }}
              </span>
              @if($selectedAudit->form_factor)
                <span @class([
                  'inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium',
                  'bg-blue-100 text-blue-700' => $selectedAudit->form_factor === 'desktop',
                  'bg-purple-100 text-purple-700' => $selectedAudit->form_factor === 'mobile',
                ])>
                  @if($selectedAudit->form_factor === 'desktop')
                    <svg class="size-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M2 4.25A2.25 2.25 0 0 1 4.25 2h11.5A2.25 2.25 0 0 1 18 4.25v8.5A2.25 2.25 0 0 1 15.75 15h-3.105a3.501 3.501 0 0 0 1.1 1.677A.75.75 0 0 1 13.26 18H6.74a.75.75 0 0 1-.484-1.323A3.501 3.501 0 0 0 7.355 15H4.25A2.25 2.25 0 0 1 2 12.75v-8.5Z" clip-rule="evenodd" />
                    </svg>
                  @else
                    <svg class="size-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                      <path d="M8 16.25a.75.75 0 0 1 .75-.75h2.5a.75.75 0 0 1 0 1.5h-2.5a.75.75 0 0 1-.75-.75Z" />
                      <path fill-rule="evenodd" d="M4 4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4Zm6 11.75a.75.75 0 0 0 0 1.5h.008a.75.75 0 0 0 0-1.5H10Z" clip-rule="evenodd" />
                    </svg>
                  @endif
                  {{ ucfirst($selectedAudit->form_factor) }}
                </span>
              @endif
            </div>
          </div>

          <div class="grid grid-cols-2 sm:grid-cols-4 gap-6">
            @php
              $categoryScores = [
                ['label' => 'Performance', 'score' => $selectedAudit->performance_score],
                ['label' => 'Accessibility', 'score' => $selectedAudit->accessibility_score],
                ['label' => 'Best Practices', 'score' => $selectedAudit->best_practices_score],
                ['label' => 'SEO', 'score' => $selectedAudit->seo_score],
              ];
              $circumference = 2 * M_PI * 40; // r=40 → ≈251.33
            @endphp

            @foreach($categoryScores as $cat)
              @php
                $score = $cat['score'];
                $offset = $circumference * (1 - $score / 100);
                $color = $score >= 90 ? '#16a34a' : ($score >= 70 ? '#d97706' : '#dc2626');
                $textColor = $score >= 90 ? 'text-green-600' : ($score >= 70 ? 'text-amber-500' : 'text-red-600');
              @endphp
              <div class="flex flex-col items-center gap-2">
                <div class="relative size-24">
                  <svg class="w-full h-full -rotate-90" viewBox="0 0 100 100">
                    <!-- Background track -->
                    <circle
                      cx="50" cy="50" r="40"
                      fill="none"
                      stroke="#e5e7eb"
                      stroke-width="8"
                    />
                    <!-- Progress arc -->
                    <circle
                      cx="50" cy="50" r="40"
                      fill="none"
                      stroke="{{ $color }}"
                      stroke-width="8"
                      stroke-linecap="round"
                      stroke-dasharray="{{ $circumference }}"
                      stroke-dashoffset="{{ $offset }}"
                    />
                  </svg>
                  <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-xl font-bold {{ $textColor }}">{{ $score }}</span>
                  </div>
                </div>
                <span class="text-xs font-medium text-gray-600 text-center">{{ $cat['label'] }}</span>
              </div>
            @endforeach
          </div>
        </div>

        <!-- Web Vitals -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-base font-semibold text-gray-900 mb-5">Web Vitals</h3>

          <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            @foreach($this->metrics as $metric)
              @php
                $ratingColors = [
                  'good' => ['bg' => 'bg-green-50', 'badge' => 'bg-green-100 text-green-700', 'label' => 'Good'],
                  'needs-improvement' => ['bg' => 'bg-amber-50', 'badge' => 'bg-amber-100 text-amber-700', 'label' => 'Needs Improvement'],
                  'poor' => ['bg' => 'bg-red-50', 'badge' => 'bg-red-100 text-red-700', 'label' => 'Poor'],
                  'unknown' => ['bg' => 'bg-gray-50', 'badge' => 'bg-gray-100 text-gray-600', 'label' => '—'],
                ];
                $rating = $ratingColors[$metric['rating']] ?? $ratingColors['unknown'];
              @endphp
              <div class="rounded-lg border border-gray-100 {{ $rating['bg'] }} p-4">
                <div class="flex items-start justify-between gap-2">
                  <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ $metric['abbr'] }}</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900">{{ $metric['value'] }}</p>
                    <p class="mt-0.5 text-xs text-gray-500">{{ $metric['label'] }}</p>
                  </div>
                  <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $rating['badge'] }}">
                    {{ $rating['label'] }}
                  </span>
                </div>
              </div>
            @endforeach
          </div>
        </div>

        <!-- Opportunities -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-base font-semibold text-gray-900 mb-5">Opportunities</h3>

          @if(count($this->opportunities) > 0)
            <ul class="divide-y divide-gray-100">
              @foreach($this->opportunities as $opportunity)
                @php
                  $score = $opportunity['score'];
                  $pill = $score < 0.5
                    ? 'bg-red-100 text-red-700'
                    : 'bg-amber-100 text-amber-700';
                  $scoreLabel = round($score * 100);
                @endphp
                <li class="py-3 flex items-start gap-3">
                  <span class="mt-0.5 shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $pill }}">
                    {{ $scoreLabel }}
                  </span>
                  <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-900">{{ $opportunity['title'] }}</p>
                    @if($opportunity['displayValue'])
                      <p class="text-xs text-gray-500 mt-0.5">{{ $opportunity['displayValue'] }}</p>
                    @endif
                    @if($opportunity['description'])
                      @php
                        $desc = preg_replace(
                            '/\[([^\]]+)\]\((https?:\/\/[^\)]+)\)/',
                            '<a href="$2" target="_blank" rel="noopener" class="underline hover:text-gray-700">$1</a>',
                            e($opportunity['description'])
                        );
                      @endphp
                      <p class="text-xs text-gray-400 mt-1 leading-relaxed">{!! $desc !!}</p>
                    @endif
                  </div>
                </li>
              @endforeach
            </ul>
          @else
            <p class="text-sm text-green-700 font-medium">No opportunities found — great work!</p>
          @endif
        </div>

    @else
      <div class="flex items-center justify-center py-20 text-gray-400 text-sm">
        No audit data available.
      </div>
    @endif

    </div><!-- /flex-1 -->
  </div>
</div>
