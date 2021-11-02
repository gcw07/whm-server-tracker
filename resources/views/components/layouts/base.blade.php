<x-html
  :title="isset($title) ? $title . ' | ' . config('app.name') : ''"
  class="bg-gray-100 h-screen antialiased leading-none"
>
  <x-slot name="head">
    <script src="{{ asset('js/app.js') }}" defer></script>

    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">

    @livewireStyles
  </x-slot>

  {{ $slot }}

  <x-layouts.footer />

  @livewire('livewire-ui-modal')
  @livewireScripts
</x-html>
