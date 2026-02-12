<div>
  <!-- Page Header -->
  <div class="pb-5 border-b border-gray-200 md:flex md:items-center md:justify-between">
    <div class="flex-1 min-w-0">
      <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('dashboard')">Home</flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('users.index')">Users</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>Edit user password</flux:breadcrumbs.item>
      </flux:breadcrumbs>

      <h3 class="mt-2 text-2xl leading-6 font-medium text-gray-900">
        Users
      </h3>
    </div>
  </div>
  <!-- / End Page Header -->

  <div class="mt-6">
    <!-- Begin content -->

    <flux:card class="bg-white">
      <flux:heading size="xl">Edit user password</flux:heading>
      <flux:subheading>Enter the required information below to edit a user password.</flux:subheading>

      <flux:separator class="my-4" />

      <form wire:submit="save" class="space-y-8">
        <flux:field>
          <flux:label>Name</flux:label>
          <div>{{ $this->form->user->name }}</div>
        </flux:field>

        <flux:input type="password" wire:model="form.password" label="Password" required />

        <flux:input type="password" wire:model="form.password_confirmation" label="Password Confirmation" required />

        <div class="pt-5 border-t border-gray-200">
          <div class="flex justify-end space-x-3">
            <flux:button :href="route('users.index')">Cancel</flux:button>
            <flux:button type="submit" variant="primary" icon="check">Save</flux:button>
          </div>
        </div>
      </form>
    </flux:card>
    <!-- /End Content -->
  </div>
</div>
