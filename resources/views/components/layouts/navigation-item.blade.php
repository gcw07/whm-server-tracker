@php
  if ($mobile) {
    $defaultClass = 'block px-3 py-2 rounded-md text-base font-medium';
    $activeClass = 'text-white bg-blue-500';
    $inActiveClass = 'text-white hover:bg-blue-500 hover:bg-opacity-75';
  } else {
    $defaultClass = 'px-3 py-2 rounded-md text-sm font-medium';
    $activeClass = 'text-white bg-blue-500';
    $inActiveClass = 'text-blue-200 hover:text-white hover:bg-opacity-75';
  }

  $class = \Illuminate\Support\Str::startsWith(request()->url(), $href) ?
    "$defaultClass $activeClass" : "$defaultClass $inActiveClass";
@endphp
<a {{ $attributes->merge(['class' => $class]) }} href="{{ $href }}">
  {{ $slot }}
</a>
