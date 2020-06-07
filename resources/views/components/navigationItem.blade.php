@php
  $defaultClass = 'px-3 py-2 rounded-md text-sm font-medium focus:outline-none focus:text-white focus:bg-blue-600';
  $activeClass = 'text-white bg-blue-500';
  $inActiveClass = 'text-blue-200 hover:text-white hover:bg-blue-500';
  $class = \Illuminate\Support\Str::startsWith(request()->url(), $href) ?
    "$defaultClass $activeClass" : "$defaultClass $inActiveClass";
@endphp
<a {{ $attributes->merge(['class' => $class]) }} href="{{ $href }}">
  {{ $slot }}
</a>
