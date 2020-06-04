<x-simple-layout>
  <div class="relative bg-gray-200">
    <div class="absolute inset-0 flex flex-col" aria-hidden="true">
      <div class="flex-1 bg-gray-100"></div>
      <div class="flex-1 bg-gray-200"></div>
    </div>
    <div class="relative max-w-xl mx-auto">
      <div class="flex flex-col min-h-screen sm:flex-row sm:items-center sm:p-8">
        <div class="flex flex-col flex-grow bg-white sm:shadow-2xl sm:rounded-lg sm:overflow-hidden">
          <div class="flex-grow flex flex-col sm:justify-center p-12">
            <div>
              <h1 class="text-gray-600 font-bold tracking-wide text-4xl text-center pb-5">Sign In</h1>
            </div>
            <div class="mt-3 pl-0">
              <form role="form" method="POST" action="{{ route('login') }}">
                @csrf
                <input type="hidden" name="remember" value="1">
                <div>
                  <label for="email">
                    <span class="form-label">
                        E-Mail Address
                    </span>
                    <span class="form-input-group">
                      <span class="form-input-icon">
                        <x-heroicon-s-mail />
                      </span>
                      <input class="form-input w-full {{ $errors->has('email') ? 'is-invalid' : '' }}" id="email"
                             type="email" name="email" value="{{ old('email') }}" required autofocus>
                    </span>
                    @if ($errors->has('email'))
                      <p class="invalid-feedback">{{ $errors->first('email') }}</p>
                    @endif
                  </label>
                </div>
                <div class="mt-4">
                  <label for="password">
                    <span class="form-label">
                        Password
                    </span>
                    <span class="form-input-group">
                      <span class="form-input-icon">
                        <x-heroicon-s-lock-closed />
                      </span>
                      <input class="form-input w-full {{ $errors->has('password') ? 'is-invalid' : '' }}" id="password"
                             type="password" name="password" required>
                    </span>
                    @if ($errors->has('password'))
                      <p class="invalid-feedback">{{ $errors->first('password') }}</p>
                    @endif
                  </label>
                </div>
                <div class="mt-6 flex items-center justify-between">
                  <button class="btn btn-blue w-full" type="submit">
                    Sign In
                  </button>
                </div>
              </form>
            </div>
          </div>
          <div class="bg-gray-100 border-t-2 border-gray-200 text-center p-4">
            <a class="inline-block align-baseline" href="{{ route('password.request') }}">
              Forgot Password?
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-simple-layout>
