@props([
    'sortable' => null,
    'direction' => null,
])

<th
  {{ $attributes->merge(['class' => 'px-6 py-3 bg-cool-gray-50'])->only('class') }}
>
  @unless ($sortable)
    <span class="flex items-center space-x-1 text-left text-xs leading-4 font-medium text-cool-gray-500 uppercase tracking-wider">{{ $slot }}</span>
  @else
    <button {{ $attributes->except('class') }} class="flex items-center space-x-1 text-left text-xs leading-4 font-bold text-cool-gray-500 uppercase tracking-wider group focus:outline-none focus:underline">
      <span>{{ $slot }}</span>
      <span>
        @if ($direction === 'asc')
          <x-heroicon-o-chevron-down class="h-3 w-3"/>
        @elseif ($direction === 'desc')
          <x-heroicon-o-chevron-up class="h-3 w-3"/>
        @else
          <x-heroicon-o-selector class="h-4 w-4"/>
        @endif
      </span>
    </button>
  @endif
</th>
