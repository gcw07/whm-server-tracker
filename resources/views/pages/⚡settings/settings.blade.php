<div>
  <!-- Page Header -->
  <div class="pb-3 border-b border-gray-200 sm:flex sm:items-center sm:justify-between">
    <h3 class="text-2xl leading-6 font-medium text-gray-900">
      Settings
    </h3>
  </div>
  <!-- / End Page Header -->

  <div class="mt-6 space-y-6">

    <!-- Google Search Console -->
    <flux:card>
      <div class="flex items-start justify-between gap-4">
        <div>
          <flux:heading size="lg">Google Search Console</flux:heading>
          <flux:subheading class="mt-1">
            Connect your Google account to pull data from the Search Console API.
          </flux:subheading>
        </div>

        @if($this->googleConnection)
          <flux:badge color="green" icon="check-circle" size="sm" inset="top bottom">Connected</flux:badge>
        @else
          <flux:badge color="zinc" icon="x-circle" size="sm" inset="top bottom">Not connected</flux:badge>
        @endif
      </div>

      <flux:separator class="my-4" />

      @if($this->googleConnection)
        <dl class="space-y-2 text-sm">
          <div class="flex gap-2">
            <dt class="font-medium text-zinc-500 w-28">Account</dt>
            <dd class="text-zinc-800 dark:text-zinc-200">{{ $this->googleConnection['email'] }}</dd>
          </div>
          @if(isset($this->googleConnection['expires_at']))
            <div class="flex gap-2">
              <dt class="font-medium text-zinc-500 w-28">Token expires</dt>
              <dd class="text-zinc-800 dark:text-zinc-200">
                {{ \Carbon\Carbon::parse($this->googleConnection['expires_at'])->format('M d, Y g:ia') }}
              </dd>
            </div>
          @endif
        </dl>

        <div class="mt-4">
          <form method="POST" action="{{ route('google.disconnect') }}">
            @csrf
            @method('DELETE')
            <flux:button type="submit" variant="danger" size="sm">Disconnect</flux:button>
          </form>
        </div>
      @else
        <div>
          <flux:button href="{{ route('google.redirect') }}" icon="arrow-top-right-on-square" size="sm">
            Connect with Google
          </flux:button>
        </div>
      @endif
    </flux:card>

  </div>
</div>
