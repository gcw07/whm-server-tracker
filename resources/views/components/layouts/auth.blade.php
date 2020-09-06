<x-layouts.base :title="$title">
  <div class="relative bg-gray-200">
    <div class="absolute inset-0 flex flex-col" aria-hidden="true">
      <div class="flex-1 bg-gray-100"></div>
      <div class="flex-1 bg-gray-200"></div>
    </div>
    <div class="relative max-w-xl mx-auto">
      <div class="flex flex-col min-h-screen sm:flex-row sm:items-center sm:p-8">
        <div class="flex flex-col flex-grow bg-white sm:shadow-2xl sm:rounded-lg sm:overflow-hidden">
          {{ $slot }}
        </div>
      </div>
    </div>
  </div>
</x-layouts.base>
