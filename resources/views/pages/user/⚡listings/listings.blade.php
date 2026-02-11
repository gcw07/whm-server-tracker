<div>
  <!-- Page Header -->
  <div class="pb-3 border-b border-gray-200 sm:flex sm:items-center sm:justify-between">
    <h3 class="text-2xl leading-6 font-medium text-gray-900">
      Users
    </h3>
    <div class="mt-3 flex md:mt-0 gap-2">
      <flux:button :href="route('users.create')" variant="primary" icon="plus">Create User</flux:button>
    </div>
  </div>
  <!-- / End Page Header -->

  <flux:card class="p-0 overflow-hidden bg-gray-50 mt-8">
    <flux:table :paginate="$this->users">
      <flux:table.columns>
        <flux:table.column class="px-6! bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">NAME</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">EMAIL</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">LAST LOGIN</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">LOGIN IP</flux:table.column>
        <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">
          <span class="sr-only">Manage</span>
        </flux:table.column>
      </flux:table.columns>

      <flux:table.rows>
        @forelse ($this->users as $user)
          <flux:table.row :key="$user->id" @class([
                'bg-gray-50' => $loop->even,
                'bg-white' => $loop->odd
            ])>
            <flux:table.cell class="px-6! py-5!">
              {{ $user->name }}
            </flux:table.cell>

            <flux:table.cell class="whitespace-nowrap">
              {{ $user->email }}
            </flux:table.cell>

            <flux:table.cell class="whitespace-nowrap">
              {{ $user->lastLogin ? $user->lastLogin->created_at->format('F j, Y, g:i a') : 'Never' }}
            </flux:table.cell>

            <flux:table.cell class="whitespace-nowrap">
              {{ $user->lastLogin?->ip_address }}
            </flux:table.cell>

            <flux:table.cell>
              <flux:dropdown position="bottom" align="end">
                <flux:button size="sm" icon="ellipsis-vertical" />

                <flux:menu>
                  <flux:menu.item :href="route('users.edit', $user)" icon="pencil-square">Edit</flux:menu.item>

                  <flux:menu.item icon="key">Change Password</flux:menu.item>

                  <flux:modal.trigger name="delete-user-modal">
                    <flux:menu.item variant="danger" icon="trash">Delete</flux:menu.item>
                  </flux:modal.trigger>
                </flux:menu>
              </flux:dropdown>
            </flux:table.cell>
          </flux:table.row>
        @empty
          <flux:table.row>
            <flux:table.cell colspan="5" class="py-8 whitespace-nowrap font-semibold text-zinc-700">
              <div class="text-center">
                <div class="flex items-center justify-center">
                  <flux:icon.magnifying-glass class="size-12" />
                </div>
                <p class="text-lg mt-6">No users matched your search.</p>
              </div>
            </flux:table.cell>
          </flux:table.row>
        @endforelse
      </flux:table.rows>
    </flux:table>
  </flux:card>

  <!-- Delete User Modal -->
  <flux:modal name="delete-user-modal">
    <div class="space-y-6">
      <div class="sm:flex sm:items-start">
        <div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:size-10 dark:bg-red-500/10">
          <flux:icon.exclamation-triangle class="text-red-500" />
        </div>
        <div class="ml-4">
          <flux:heading size="lg">Delete user?</flux:heading>
          <flux:text class="mt-2">
            Are you sure you want to delete the user <span class="text-zinc-800 font-semibold">"user name"</span>?
            The server information and all associated accounts will be permanently removed. This action cannot be undone.
          </flux:text>
        </div>
      </div>
      <div class="flex gap-2">
        <flux:spacer />
        <flux:modal.close>
          <flux:button>Cancel</flux:button>
        </flux:modal.close>
        <flux:button wire:click="delete" icon="trash" variant="danger">Delete server</flux:button>
      </div>
    </div>
  </flux:modal>
  <!-- /End Delete Server Modal -->
</div>
