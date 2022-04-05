@error($field, $bag)
<p {{ $attributes->merge(['class' => 'mt-2 text-sm text-red-600']) }}>
  @if ($slot->isEmpty())
    {{ $message }}
  @else
    {{ $slot }}
  @endif
</p>
@enderror
