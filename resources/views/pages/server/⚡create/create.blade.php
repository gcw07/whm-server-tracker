<div>
  <!-- Page Header -->
  <div class="pb-5 border-b border-gray-200 sm:flex sm:items-center sm:justify-between">
    <h3 class="text-2xl leading-6 font-medium text-gray-900">
      Servers
    </h3>
    <div class="mt-3 sm:mt-0 sm:ml-4">
    </div>
  </div>
  <!-- / End Page Header -->

  <div class="mt-6">
    <!-- Begin content -->

    <flux:card class="bg-white">
      <flux:heading size="xl">Create new server</flux:heading>

      <flux:separator />

      <form wire:submit="save" class="space-y-8 mt-8">
        <flux:input wire:model="form.name" label="Name" />

        <flux:input wire:model="form.address" label="IP Address" />

        <flux:input wire:model="form.port" label="Port" />

        <flux:radio.group wire:model="form.serverType" label="Server Type" variant="cards" class="max-sm:flex-col">
          <flux:radio value="dedicated" label="Dedicated" />
          <flux:radio value="reseller" label="Reseller" />
          <flux:radio value="vps" label="VPS" />
        </flux:radio.group>

        <flux:textarea wire:model="form.notes" label="Notes" />

        <flux:input wire:model="form.token" label="API Token" description="This is a WHM API Token. Once set this will not be visible again." />

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
