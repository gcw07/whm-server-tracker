@foreach ($routes as $route)
  <a href="{{ $route['url'] }}"
    @class([
    'flex items-center rounded-md px-3 py-2 text-base text-gray-900 font-medium hover:bg-gray-100 hover:text-gray-800',
    'bg-gray-100 text-gray-800' => $route['active'],
    ])>
    <x-dynamic-component :component="$route['icon']" class="-ml-0.5 mr-2 h-4 w-4" />
    {{ $route['name'] }}
  </a>
@endforeach
