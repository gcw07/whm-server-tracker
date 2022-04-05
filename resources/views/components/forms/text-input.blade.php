@props(['disabled' => false])

<div class="mt-1">
  <input {{ $disabled ? 'disabled' : '' }}
         name="{{ $name }}"
         type="{{ $type }}"
         id="{{ $id }}"
         @if($value)value="{{ $value }}"@endif
    {!! $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block w-full sm:text-sm']) !!}>
</div>
