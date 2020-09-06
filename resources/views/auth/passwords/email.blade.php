<x-layouts.auth title="Reset Password">

  <div class="flex-grow flex flex-col sm:justify-center p-12">
    <div>
      <h1 class="text-gray-600 font-bold tracking-wide text-4xl text-center pb-5">Reset Password</h1>
    </div>

    @if (session('status'))
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('status') }}</span>
      </div>
    @endif

    <div class="mt-3 pl-0">

      <x-form :action="route('password.email')">

        <div>
          <label for="email">
            <span class="form-label">
              E-Mail Address
            </span>
            <span class="form-input-group">
              <span class="form-input-icon">
                <x-heroicon-s-mail/>
              </span>
              <x-email name="email" required autofocus
                       class="form-input w-full {{ $errors->has('email') ? 'is-invalid' : '' }}"/>
            </span>
            <x-error field="email" class="invalid-feedback"/>
          </label>
        </div>
        <div class="mt-6 flex items-center justify-between">
          <button class="btn btn-blue w-full" type="submit">
            Send Password Reset Link
          </button>
        </div>

      </x-form>
    </div>
  </div>

</x-layouts.auth>>
