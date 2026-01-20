<div class="h-full">
  <!-- Page Header -->
  <div class="pb-5 border-b border-gray-200 md:flex md:items-center md:justify-between">
    <div class="flex-1 min-w-0">
      <nav class="flex" aria-label="Breadcrumb">
        <ol role="list" class="flex items-center space-x-4">
          <li>
            <div class="flex">
              <a href="{{ route('monitors.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">Monitors</a>
            </div>
          </li>
          <li>
            <div class="flex items-center">
              <x-heroicon-s-chevron-right class="shrink-0 h-5 w-5 text-gray-400"/>
              <span class="ml-4 text-sm font-medium text-gray-500">Lighthouse Report</span>
            </div>
          </li>
        </ol>
      </nav>
      <h3 class="mt-2 text-2xl leading-6 font-medium text-gray-900">
        {{ preg_replace("(^https?://)", "", $monitor->url ) }}
      </h3>
    </div>
  </div>
  <!-- / End Page Header -->


  <div class="mt-6 h-full">
    <!-- Begin content -->

    <div>
      <dl class="mt-5 grid grid-cols-1">
        <div class="bg-white shadow rounded-lg divide-y divide-gray-200">
          <div class="bg-gray-50 rounded-lg px-4 py-5 sm:px-6">
            <div class="-ml-4 -mt-4 flex justify-between items-center flex-wrap sm:flex-nowrap">
              <div class="ml-4 mt-4 flex items-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Reports</h3>
              </div>
            </div>
          </div>
          <div class="py-5 px-6">
            <ul class="list-disc list-inside pl-4">
              @forelse($audits as $audit)
                <li wire:click.prevent="changeSelected({{ $audit->id }})"
                  @class([
                    'text-gray-500 cursor-pointer truncate hover:text-gray-900',
                    'font-bold' => $audit->id === $selectedAudit->id,
                    'font-medium' => $audit->id !== $selectedAudit->id,
                  ])>
                  {{ $audit->date->format("D, F j, Y") }}
                </li>
              @empty
                <p>There are no recent audits. Please wait for one to be processed.</p>
              @endforelse
            </ul>

          </div>
        </div>
      </dl>
    </div>

    <div class="mt-6 text-lg font-medium text-gray-700">Report
      from {{ $selectedAudit->date->format("l, F j, Y") }}</div>

    <iframe src="{{ route('monitors.lighthouse-iframe', $selectedAudit->id) }}" class="w-full h-screen mt-4" height="100%"></iframe>

    <!-- /End Content -->
  </div>

</div>

