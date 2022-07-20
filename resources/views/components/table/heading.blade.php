@props([
    'sortable' => null,
    'direction' => null,
])

<th
  scope="col" {{ $attributes->merge(['class' => 'px-6 py-3 text-left text-xs bg-gray-50 text-gray-500 uppercase tracking-wider'])->only('class') }}
>
  @unless ($sortable)
    <span class="flex items-center space-x-1 leading-4 font-medium">{{ $slot }}</span>
  @else
    <button type="button" {{ $attributes->except('class') }} class="flex items-center space-x-1 leading-4 font-bold uppercase tracking-wider group focus:outline-none focus:underline">
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
