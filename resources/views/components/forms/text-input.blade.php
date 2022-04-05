@props(['disabled' => false])

<div class="mt-1">
  <input {{ $disabled ? 'disabled' : '' }}
         name="{{ $name }}"
         type="{{ $type }}"
         id="{{ $id }}"
         @if($value)value="{{ $value }}"@endif
         {{ $attributes->class([
            'focus:ring focus:ring-opacity-50 rounded-md shadow-sm block w-full sm:text-sm',
            'border-gray-300 focus:border-indigo-300 focus:ring-indigo-200' => ! $hasErrors(),
            'border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500' => $hasErrors(),
         ]) }}>
</div>
