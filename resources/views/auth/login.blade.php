<x-layouts.guest title="Sign In">
  <div class="flex flex-col flex-grow p-12 sm:justify-center">
    <div>
      <h1 class="pb-5 text-4xl font-bold tracking-wide text-center text-gray-600">Sign In</h1>
    </div>
    <div class="pl-0 mt-3">

      <x-forms.form :action="route('login')">
        <input type="hidden" name="remember" value="1">

        <div>
          <x-forms.label for="email" value="E-Mail Address" required></x-forms.label>
          <x-forms.email-input id="email" name="email" placeholder="you@example.com" required autofocus></x-forms.email-input>
          <x-forms.error field="email"></x-forms.error>
        </div>

        <div class="mt-4">
          <x-forms.label for="password" value="Password" required></x-forms.label>
          <x-forms.text-input id="password" type="password" name="password" required></x-forms.text-input>
          <x-forms.error field="password"></x-forms.error>
        </div>

        <div class="flex items-center justify-between mt-6">
          <x-forms.button class="w-full">Sign In</x-forms.button>
        </div>
      </x-forms.form>

    </div>
  </div>
  <div class="p-4 text-center bg-gray-100 border-t-2 border-gray-200">
    <a class="inline-block align-baseline" href="{{ route('password.request') }}">
      Forgot Password?
    </a>
  </div>
</x-layouts.guest>
