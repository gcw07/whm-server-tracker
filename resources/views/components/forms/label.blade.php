<label for="{{ $for }}" {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700']) }}>
  {{ $fallback }}
  @if($required)*@endif
</label>
