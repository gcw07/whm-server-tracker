@props([
    'sortable' => null,
    'direction' => null,
])

<th
  {{ $attributes->merge(['class' => 'px-6 py-3 bg-cool-gray-50'])->only('class') }}
>
  @unless ($sortable)
    <span class=" text-left text-xs leading-4 font-medium text-cool-gray-500 uppercase tracking-wider">{{ $slot }}</span>
  @else
    <button {{ $attributes->except('class') }} class="flex items-center space-x-1 text-left text-xs leading-4 font-medium text-cool-gray-500 uppercase tracking-wider group focus:outline-none focus:underline">
      <span>{{ $slot }}</span>
      <span>
        @if ($direction === 'asc')
          <x-heroicon-o-chevron-down class="h-3 w-3"/>
        @elseif ($direction === 'desc')
          <x-heroicon-o-chevron-up class="h-3 w-3"/>
        @else
          <svg class="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
        @endif
      </span>
    </button>
  @endif
</th>
