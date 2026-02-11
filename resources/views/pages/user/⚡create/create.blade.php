<div>
  <!-- Page Header -->
  <div class="pb-5 border-b border-gray-200 md:flex md:items-center md:justify-between">
    <div class="flex-1 min-w-0">
      <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('dashboard')">Home</flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('users.index')">Users</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>Create new user</flux:breadcrumbs.item>
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
      <flux:heading size="xl">Create new user</flux:heading>
      <flux:subheading>Enter the required information below to add a new user.</flux:subheading>

      <flux:separator class="my-4" />

      <form wire:submit="save" class="space-y-8">
        <flux:input wire:model="form.name" label="Name" required />

        <flux:input type="email" wire:model="form.email" label="Email" required />

        <flux:input type="password" wire:model="form.password" label="Password" required />

        <flux:input type="password" wire:model="form.password_confirmation" label="Password Confirmation" required />

        <flux:heading size="xl">Notifications</flux:heading>
        <flux:subheading>Be notified when events happen to the various websites you track.</flux:subheading>

        <flux:separator class="my-4" />

        <flux:checkbox.group wire:model="form.notification_types" label="By Email">
          <flux:checkbox label="Uptime Check Succeeded" value="uptime_check_succeeded" description="Get notified when an uptime check succeeds." />
          <flux:checkbox label="Uptime Check Failed" value="uptime_check_failed" description="Get notified when an uptime check failed." />
          <flux:checkbox label="Uptime Check Recovered" value="uptime_check_recovered" description="Get notified when an uptime check recovers after a failure." />
          <flux:checkbox label="SSL Certificate Check Succeeded" value="certificate_check_succeeded" description="Get notified when an SSL certificate check succeeds." />
          <flux:checkbox label="SSL Certificate Check Failed" value="certificate_check_failed" description="Get notified when an SSL certificate check failed." />
          <flux:checkbox label="SSL Certificate Expires Soon" value="certificate_expires_soon" description="Get notified when an SSL certificate is expiring soon." />
          <flux:checkbox label="Fetched Remote Server Data Succeeded" value="fetched_server_data_succeeded" description="Get notified when fetching the remote server data succeeds." />
          <flux:checkbox label="Fetched Remote Server Data Failed" value="fetched_server_data_failed" description="Get notified when fetching the remote server data failed." />
          <flux:checkbox label="Domain Name Expires Soon" value="domain_name_expires_soon" description="Get notified when the domain name is expiring soon." />
        </flux:checkbox.group>

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
