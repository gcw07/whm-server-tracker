<x-layouts.guest title="Sign In">
  <div class="flex flex-col flex-grow p-12 sm:justify-center">
    <div>
      <h1 class="pb-5 text-4xl font-bold tracking-wide text-center text-gray-600">Sign In</h1>
    </div>
    <div class="pl-0 mt-3">

{{--      <x-form :action="route('login')">--}}
{{--        <input type="hidden" name="remember" value="1">--}}

{{--        <div>--}}
{{--          <label for="email">--}}
{{--            <span class="form-label">--}}
{{--              E-Mail Address--}}
{{--            </span>--}}
{{--            <span class="form-input-group">--}}
{{--              <span class="form-input-icon">--}}
{{--                <x-heroicon-s-mail/>--}}
{{--              </span>--}}
{{--              <x-email name="email" required autofocus--}}
{{--                       class="form-input w-full {{ $errors->has('email') ? 'is-invalid' : '' }}"/>--}}
{{--            </span>--}}
{{--            <x-error field="email" class="invalid-feedback"/>--}}
{{--          </label>--}}
{{--        </div>--}}
{{--        <div class="mt-4">--}}
{{--          <label for="password">--}}
{{--            <span class="form-label">--}}
{{--              Password--}}
{{--            </span>--}}
{{--            <span class="form-input-group">--}}
{{--              <span class="form-input-icon">--}}
{{--                <x-heroicon-s-lock-closed/>--}}
{{--              </span>--}}
{{--              <x-password name="password" required--}}
{{--                          class="form-input w-full {{ $errors->has('password') ? 'is-invalid' : '' }}"/>--}}
{{--            </span>--}}
{{--            <x-error field="password" class="invalid-feedback"/>--}}
{{--          </label>--}}
{{--        </div>--}}
{{--        <div class="flex items-center justify-between mt-6">--}}
{{--          <button class="w-full btn btn-blue" type="submit">--}}
{{--            Sign In--}}
{{--          </button>--}}
{{--        </div>--}}

{{--      </x-form>--}}

    </div>
  </div>
  <div class="p-4 text-center bg-gray-100 border-t-2 border-gray-200">
    <a class="inline-block align-baseline" href="{{ route('password.request') }}">
      Forgot Password?
    </a>
  </div>
</x-layouts.guest>
