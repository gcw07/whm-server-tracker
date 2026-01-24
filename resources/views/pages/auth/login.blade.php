<x-layouts::simple>
  <div class="sm:mx-auto sm:w-full sm:max-w-md">
    <img src="/images/logo-solid.svg" alt="{{ config('app.name') }}" class="mx-auto h-20 w-auto dark:hidden" />
    <img src="/images/logo.svg" alt="{{ config('app.name') }}" class="mx-auto h-20 w-auto not-dark:hidden" />
    <h2 class="mt-6 text-center text-2xl/9 font-bold tracking-tight text-gray-900 dark:text-white">Sign in to your account</h2>
  </div>

  <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
    <div class="bg-white px-6 py-12 shadow-sm sm:rounded-lg sm:px-12 dark:bg-gray-800/50 dark:shadow-none dark:outline dark:-outline-offset-1 dark:outline-white/10">
      <form action="{{ route('login.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <flux:input
          name="email"
          label="Email address"
          :value="old('email')"
          type="email"
          required
          autofocus
          autocomplete="email"
          placeholder="email@example.com"
        />

        <!-- Password -->
        <div class="relative">
          <flux:input
            name="password"
            label="Password"
            type="password"
            required
            autocomplete="current-password"
            placeholder="Password"
            viewable
          />

          @if (Route::has('password.request'))
            <flux:link class="absolute top-0 text-sm end-0" :href="route('password.request')" wire:navigate>
              {{ __('Forgot your password?') }}
            </flux:link>
          @endif
        </div>

        <!-- Remember Me -->
        <flux:checkbox name="remember" :label="__('Remember me')" :checked="old('remember')" />

        <div class="flex items-center justify-end">
          <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
            Sign in
          </flux:button>
        </div>
      </form>

    </div>
  </div>
</x-layouts::simple>
