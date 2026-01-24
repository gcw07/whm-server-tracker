<x-layouts::simple>
  <div class="sm:mx-auto sm:w-full sm:max-w-md">
    <img src="/images/logo-solid.svg" alt="{{ config('app.name') }}" class="mx-auto h-20 w-auto dark:hidden" />
    <img src="/images/logo.svg" alt="{{ config('app.name') }}" class="mx-auto h-20 w-auto not-dark:hidden" />
    <h2 class="mt-6 text-center text-2xl/9 font-bold tracking-tight text-gray-900 dark:text-white">Please enter your new password below</h2>
  </div>

  <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
    <div class="bg-white px-6 py-12 shadow-sm sm:rounded-lg sm:px-12 dark:bg-gray-800/50 dark:shadow-none dark:outline dark:-outline-offset-1 dark:outline-white/10">
      <form action="{{ route('password.update') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Token -->
        <input type="hidden" name="token" value="{{ request()->route('token') }}">

        <!-- Email Address -->
        <flux:input
          name="email"
          value="{{ request('email') }}"
          label="Email"
          type="email"
          required
          autocomplete="email"
        />

        <!-- Password -->
        <flux:input
          name="password"
          label="Password"
          type="password"
          required
          autocomplete="new-password"
          placeholder="Password"
          viewable
        />

        <!-- Confirm Password -->
        <flux:input
          name="password_confirmation"
          label="Confirm password"
          type="password"
          required
          autocomplete="new-password"
          placeholder="Confirm password"
          viewable
        />

        <flux:button variant="primary" type="submit" class="w-full" data-test="email-password-reset-link-button">
          Reset password
        </flux:button>
      </form>

    </div>
  </div>
</x-layouts::simple>
