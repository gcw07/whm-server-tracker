<x-table>
  <x-slot name="head">
    <x-table.heading sortable multi-column wire:click="sortBy('name')" :direction="$sorts['name'] ?? null">Name</x-table.heading>
    <x-table.heading sortable multi-column wire:click="sortBy('email')" :direction="$sorts['email'] ?? null">Email</x-table.heading>
    <x-table.heading>Last Login</x-table.heading>
    <x-table.heading>Last IP Address</x-table.heading>
    <x-table.heading />
  </x-slot>
  <x-slot name="body">
    @forelse ($users as $user)
      <x-table.row wire:loading.class.delay="opacity-50" wire:key="row-{{ $user->id }}">
        <x-table.cell>
          {{ $user->name }}
        </x-table.cell>

        <x-table.cell>
          {{ $user->email }}
        </x-table.cell>

        <x-table.cell>
          date
        </x-table.cell>

        <x-table.cell>
          IP
        </x-table.cell>

        <x-table.cell>
{{--          <x-button.link wire:click="edit({{ $transaction->id }})">Edit</x-button.link>--}}
        </x-table.cell>
      </x-table.row>
    @empty
      <x-table.row>
        <x-table.cell colspan="6">
          <div class="flex justify-center items-center space-x-2">
            <x-heroicon-o-inbox class="h-8 w-8 text-cool-gray-400"/>
            <span class="font-medium py-8 text-cool-gray-400 text-xl">No transactions found...</span>
          </div>
        </x-table.cell>
      </x-table.row>
    @endforelse
  </x-slot>
</x-table>
