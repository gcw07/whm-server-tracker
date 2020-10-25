<x-table>
  <x-slot name="head">
    <x-table.heading sortable wire:click="sortBy('name')" :direction="$sortField === 'name' ? $sortDirection : null">Name</x-table.heading>
    <x-table.heading sortable wire:click="sortBy('email')" :direction="$sortField === 'email' ? $sortDirection : null">Email</x-table.heading>
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
          {{ $user->lastLogin->created_at->diffForHumans() }}
        </x-table.cell>

        <x-table.cell>
          {{ $user->lastLogin->ip_address }}
        </x-table.cell>

        <x-table.cell>
{{--          <x-button.link wire:click="edit({{ $transaction->id }})">Edit</x-button.link>--}}
        </x-table.cell>
      </x-table.row>
    @empty
      <x-table.row>
        <x-table.cell colspan="5">
          <div class="flex justify-center items-center space-x-2">
            <x-heroicon-o-user class="h-8 w-8 text-cool-gray-400"/>
            <span class="font-medium py-8 text-cool-gray-400 text-xl">No users found...</span>
          </div>
        </x-table.cell>
      </x-table.row>
    @endforelse
  </x-slot>
</x-table>
