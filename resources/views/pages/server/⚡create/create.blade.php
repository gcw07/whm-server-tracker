<div>
  <!-- Page Header -->
  <div class="pb-5 border-b border-gray-200 md:flex md:items-center md:justify-between">
    <div class="flex-1 min-w-0">
      <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('dashboard')">Home</flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('servers.index')">Servers</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>Create new server</flux:breadcrumbs.item>
      </flux:breadcrumbs>

      <h3 class="mt-2 text-2xl leading-6 font-medium text-gray-900">
        Servers
      </h3>
    </div>
  </div>
  <!-- / End Page Header -->

  <div class="mt-6">
    <!-- Begin content -->

    <flux:card class="bg-white">
      <flux:heading size="xl">Create new server</flux:heading>
      <flux:subheading>Enter the required information below to add a new server and start tracking.</flux:subheading>

      <flux:separator class="my-4" />

      <form wire:submit="save" class="space-y-8">
        <flux:input wire:model="form.name" label="Name" required />

        <flux:input wire:model="form.address" label="IP Address" required />

        <flux:input wire:model="form.port" label="Port" required />

        <flux:radio.group wire:model="form.serverType" label="Server Type" variant="cards" class="max-sm:flex-col" required>
          <flux:radio value="dedicated" label="Dedicated" />
          <flux:radio value="reseller" label="Reseller" />
          <flux:radio value="vps" label="VPS" />
        </flux:radio.group>

        <flux:textarea wire:model="form.notes" label="Notes" />

        <flux:input wire:model="form.token" label="API Token" description:trailing="This is a WHM API Token. Once set this will not be visible again." />

        <div class="pt-5 border-t border-gray-200">
          <div class="flex justify-end space-x-3">
            <flux:button :href="route('servers.index')">Cancel</flux:button>
            <flux:button type="submit" variant="primary" icon="check">Save</flux:button>
          </div>
        </div>
      </form>
    </flux:card>
    <!-- /End Content -->
  </div>
</div>
