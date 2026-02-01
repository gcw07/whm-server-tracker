<x-layouts.guest title="Reset Password">

  <div class="flex-grow flex flex-col sm:justify-center p-12">
    <div>
      <h1 class="text-gray-600 font-bold tracking-wide text-4xl text-center pb-5">Reset Password</h1>
    </div>

    @session('status')
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ $value }}</span>
      </div>
    @endsession

    <div class="mt-3 pl-0">

      <x-forms.form :action="route('password.email')">

        <div>
          <x-forms.label for="email" value="E-Mail Address" required></x-forms.label>
          <x-forms.email-input id="email" name="email" placeholder="you@example.com" required autofocus></x-forms.email-input>
          <x-forms.error field="email"></x-forms.error>
        </div>

        <div class="flex items-center justify-between mt-6">
          <x-forms.button class="w-full">Send Password Reset Link</x-forms.button>
        </div>
      </x-forms.form>
    </div>
  </div>

</x-layouts.guest>
