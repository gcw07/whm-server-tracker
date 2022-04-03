@foreach ($routes as $route)
  <a href="{{ $route['url'] }}"
    @class([
    'inline-flex items-center rounded-md py-2 px-3 text-sm font-medium',
    'bg-sky-700 text-white' => $route['active'],
    'text-white hover:bg-sky-500 hover:bg-opacity-75' => !$route['active']
    ])>
    <x-heroicon-s-globe-alt class="-ml-0.5 mr-2 h-4 w-4" />
    {{ $route['name'] }}
  </a>
@endforeach
