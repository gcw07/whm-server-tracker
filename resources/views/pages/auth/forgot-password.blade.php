<x-layouts::simple>
  <div class="sm:mx-auto sm:w-full sm:max-w-md">
    <img src="/images/logo-solid.svg" alt="{{ config('app.name') }}" class="mx-auto h-20 w-auto dark:hidden" />
    <img src="/images/logo.svg" alt="{{ config('app.name') }}" class="mx-auto h-20 w-auto not-dark:hidden" />
    <h2 class="mt-6 text-center text-2xl/9 font-bold tracking-tight text-gray-900 dark:text-white">Enter your email to receive a password reset link</h2>
  </div>

  <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
    <div class="bg-white px-6 py-12 shadow-sm sm:rounded-lg sm:px-12 dark:bg-gray-800/50 dark:shadow-none dark:outline dark:-outline-offset-1 dark:outline-white/10">
      <form action="{{ route('password.email') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <flux:input
          name="email"
          label="Email Address"
          type="email"
          required
          autofocus
          placeholder="email@example.com"
        />

        <flux:button variant="primary" type="submit" class="w-full" data-test="email-password-reset-link-button">
          Email password reset link
        </flux:button>
      </form>

    </div>
  </div>

  <div class="flex flex-col gap-6 mt-4">
    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-400">
      <span>Or, return to</span>
      <flux:link :href="route('login')" wire:navigate>{{ __('log in') }}</flux:link>
    </div>
  </div>
</x-layouts::simple>
