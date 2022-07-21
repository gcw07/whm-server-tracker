<x-layouts.guest title="Reset Password">

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
      <x-forms.form :action="route('password.update')">
        <input type="hidden" name="token" value="{{ $token }}">

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

        <div class="mt-4">
          <x-forms.label for="password_confirmation" value="Confirm Password" required></x-forms.label>
          <x-forms.text-input id="password_confirmation" type="password" name="password_confirmation" required></x-forms.text-input>
          <x-forms.error field="password_confirmation"></x-forms.error>
        </div>

        <div class="flex items-center justify-between mt-6">
          <x-forms.button class="w-full"> Reset Password</x-forms.button>
        </div>
      </x-forms.form>

    </div>
  </div>

</x-layouts.guest>
