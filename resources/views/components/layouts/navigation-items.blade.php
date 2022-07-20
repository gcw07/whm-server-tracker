@foreach ($routes as $route)
  @unless($route['mobileOnly'])
    <a href="{{ $route['url'] }}"
      @class([
      'inline-flex items-center rounded-md py-2 px-3 text-sm font-medium',
      'bg-sky-700 text-white' => $route['active'],
      'text-white hover:bg-sky-500 hover:bg-opacity-75' => !$route['active']
      ])>
      <x-dynamic-component :component="$route['icon']" class="-ml-0.5 mr-2 h-4 w-4" />
      {{ $route['name'] }}
    </a>
  @endunless
@endforeach
