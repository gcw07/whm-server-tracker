<x-layouts.base :title="$title">
  <x-layouts.navigation />

  <header class="bg-white shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex items-center justify-between">
      <div class="flex-1 min-w-0">
        <h1 class="text-3xl font-bold leading-tight text-gray-900">
          {{ $title }}
        </h1>
      </div>
      @isset($actions)
        <div class="mt-4 flex md:mt-0 md:ml-4">
          {{ $actions }}
        </div>
      @endisset
    </div>
  </header>
  <main>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      {{ $slot }}
    </div>
  </main>
</x-layouts.base>
