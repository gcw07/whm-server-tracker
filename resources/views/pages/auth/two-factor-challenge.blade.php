<x-layouts::simple>
  <div class="relative w-full h-auto"
       x-cloak
       x-data="{
                showRecoveryInput: @js($errors->has('recovery_code')),
                code: '',
                recovery_code: '',
                toggleInput() {
                    this.showRecoveryInput = !this.showRecoveryInput;

                    this.code = '';
                    this.recovery_code = '';

                    $dispatch('clear-2fa-auth-code');

                    $nextTick(() => {
                        this.showRecoveryInput
                            ? this.$refs.recovery_code?.focus()
                            : $dispatch('focus-2fa-auth-code');
                    });
                },
            }"
    >

    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <img src="/images/logo-solid.svg" alt="{{ config('app.name') }}" class="mx-auto h-20 w-auto dark:hidden" />
      <img src="/images/logo.svg" alt="{{ config('app.name') }}" class="mx-auto h-20 w-auto not-dark:hidden" />

      <div x-show="!showRecoveryInput">
        <h2 class="mt-6 text-center text-2xl/9 font-bold tracking-tight text-gray-900 dark:text-white">Enter the authentication code provided by your authenticator application.</h2>
      </div>

      <div x-show="showRecoveryInput">
        <h2 class="mt-6 text-center text-2xl/9 font-bold tracking-tight text-gray-900 dark:text-white">Please confirm access to your account by entering one of your emergency recovery codes.</h2>
      </div>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
      <div class="bg-white px-6 py-12 shadow-sm sm:rounded-lg sm:px-12 dark:bg-gray-800/50 dark:shadow-none dark:outline dark:-outline-offset-1 dark:outline-white/10">
        <form action="{{ route('two-factor.login.store') }}" method="POST" class="space-y-6">
          @csrf

          <div class="space-y-5 text-center">
            <div x-show="!showRecoveryInput">
              <div class="flex items-center justify-center my-5">
                <flux:otp
                  x-model="code"
                  length="6"
                  name="code"
                  label="OTP Code"
                  label:sr-only
                  class="mx-auto"
                />
              </div>
            </div>

            <div x-show="showRecoveryInput">
              <div class="my-5">
                <flux:input
                  type="text"
                  name="recovery_code"
                  x-ref="recovery_code"
                  x-bind:required="showRecoveryInput"
                  autocomplete="one-time-code"
                  x-model="recovery_code"
                />
              </div>

              @error('recovery_code')
              <flux:text color="red">
                {{ $message }}
              </flux:text>
              @enderror
            </div>

            <flux:button
              variant="primary"
              type="submit"
              class="w-full"
            >
              {{ __('Continue') }}
            </flux:button>
          </div>

          <div class="mt-5 space-x-0.5 text-sm leading-5 text-center">
            <span class="opacity-50">{{ __('or you can') }}</span>
            <div class="inline font-medium underline cursor-pointer opacity-80">
              <span x-show="!showRecoveryInput" @click="toggleInput()">{{ __('login using a recovery code') }}</span>
              <span x-show="showRecoveryInput" @click="toggleInput()">{{ __('login using an authentication code') }}</span>
            </div>
          </div>
        </form>

      </div>
    </div>
  </div>
</x-layouts::simple>
