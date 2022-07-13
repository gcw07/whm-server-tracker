<div x-data="{ open: false }" @click.away="open = false" @close.stop="open = false" {{ $attributes->merge(['class' => 'relative']) }}>
  <div @click="open = ! open">
    {{ $trigger }}
  </div>

  <div x-show="open"
       @click="open = false"
       x-transition:enter="transition ease-out duration-100"
       x-transition:enter-start="transform opacity-0 scale-95"
       x-transition:enter-end="transform opacity-100 scale-100"
       x-transition:leave="transition ease-in duration-75"
       x-transition:leave-start="transform opacity-100 scale-100"
       x-transition:leave-end="transform opacity-0 scale-95"
       x-cloak>
    {{ $slot }}
  </div>
</div>
