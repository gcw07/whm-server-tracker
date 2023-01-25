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
              <x-heroicon-s-chevron-right class="flex-shrink-0 h-5 w-5 text-gray-400" />
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

    <iframe src="{{ route('monitors.lighthouse-iframe', $monitor->id) }}" class="w-full h-screen" height="100%"></iframe>

    <!-- /End Content -->
  </div>

</div>

