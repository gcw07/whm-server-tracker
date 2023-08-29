<div>
  <!-- Page Header -->
  <div class="pb-5 border-b border-gray-200 sm:flex sm:items-center sm:justify-between">
    <h3 class="text-2xl leading-6 font-medium text-gray-900">
      Users
    </h3>
    <div class="mt-3 sm:mt-0 sm:ml-4">
    </div>
  </div>
  <!-- / End Page Header -->

  <div class="mt-6">
    <!-- Begin content -->

    <div class="shadow bg-white px-6 py-4 sm:rounded-lg">

      <form wire:submit.prevent="save" class="space-y-8 divide-y divide-gray-200">
        <div class="space-y-8 divide-y divide-gray-200 sm:space-y-5">
          <div>
            <div>
              <h3 class="text-lg leading-6 font-medium text-gray-900 mb-1">Personal Information</h3>
            </div>
            <div class="space-y-6 sm:space-y-5">
              <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
{{--                <x-forms.label for="name" :required="true" />--}}
{{--                <x-forms.text-input name="name" class="sm:mt-0 sm:col-span-2"></x-forms.text-input>--}}
                <label for="name" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2"> Name </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                  <input type="text" wire:model.defer="state.name" id="name" autocomplete="given-name" class="block max-w-lg w-full shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm border-gray-300 rounded-md">
                  @error('state.name') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
              </div>

              <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                <label for="email" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2"> Email address </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                  <input id="email" wire:model.defer="state.email" type="email" autocomplete="email" class="block max-w-lg w-full shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm border-gray-300 rounded-md">
                  @error('state.email') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
              </div>

              <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                <label for="password" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2"> Password </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                  <input type="password" wire:model.defer="state.password" id="password" class="block max-w-lg w-full shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm border-gray-300 rounded-md">
                  @error('state.password') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
              </div>

              <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2"> Confirm Password </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                  <input type="password" wire:model.defer="state.password_confirmation" id="password_confirmation" class="block max-w-lg w-full shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm border-gray-300 rounded-md">
                  @error('state.password_confirmation') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="space-y-6 divide-y divide-gray-200 pt-8 sm:space-y-5 sm:pt-10">
          <div>
            <h3 class="text-lg font-medium leading-6 text-gray-900">Notifications</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Be notified when events happen to the various sites you track.</p>
          </div>
          <div class="space-y-6 divide-y divide-gray-200 sm:space-y-5">
            <div class="pt-6 sm:pt-5">
              <div role="group" aria-labelledby="label-email">
                <div class="sm:grid sm:grid-cols-3 sm:items-baseline sm:gap-4">
                  <div>
                    <div class="text-base font-medium text-gray-900 sm:text-sm sm:text-gray-700" id="label-email">By Email</div>
                  </div>
                  <div class="mt-4 sm:col-span-2 sm:mt-0">
                    <div class="max-w-lg space-y-4">
                      <div class="relative flex items-start">
                        <div class="flex h-5 items-center">
                          <input id="uptime_check_succeeded" wire:model.defer="state.notification_types.uptime_check_succeeded" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                        </div>
                        <div class="ml-3 text-sm">
                          <label for="uptime_check_succeeded" class="font-medium text-gray-700">Uptime Check Succeeded</label>
                          <p class="text-gray-500">Get notified when an uptime check succeeds.</p>
                          @error('state.notification_types.uptime_check_succeeded') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                      </div>
                      <div class="relative flex items-start">
                        <div class="flex h-5 items-center">
                          <input id="uptime_check_failed" wire:model.defer="state.notification_types.uptime_check_failed" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                        </div>
                        <div class="ml-3 text-sm">
                          <label for="uptime_check_failed" class="font-medium text-gray-700">Uptime Check Failed</label>
                          <p class="text-gray-500">Get notified when an uptime check failed.</p>
                          @error('state.notification_types.uptime_check_failed') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                      </div>
                      <div class="relative flex items-start">
                        <div class="flex h-5 items-center">
                          <input id="uptime_check_recovered" wire:model.defer="state.notification_types.uptime_check_recovered" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                        </div>
                        <div class="ml-3 text-sm">
                          <label for="uptime_check_recovered" class="font-medium text-gray-700">Uptime Check Recovered</label>
                          <p class="text-gray-500">Get notified when an uptime check recovers after a failure.</p>
                          @error('state.notification_types.uptime_check_recovered') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                      </div>
                      <div class="relative flex items-start">
                        <div class="flex h-5 items-center">
                          <input id="certificate_check_succeeded" wire:model.defer="state.notification_types.certificate_check_succeeded" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                        </div>
                        <div class="ml-3 text-sm">
                          <label for="certificate_check_succeeded" class="font-medium text-gray-700">SSL Certificate Check Succeeded</label>
                          <p class="text-gray-500">Get notified when an SSL certificate check succeeds.</p>
                          @error('state.notification_types.certificate_check_succeeded') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                      </div>
                      <div class="relative flex items-start">
                        <div class="flex h-5 items-center">
                          <input id="certificate_check_failed" wire:model.defer="state.notification_types.certificate_check_failed" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                        </div>
                        <div class="ml-3 text-sm">
                          <label for="certificate_check_failed" class="font-medium text-gray-700">SSL Certificate Check Failed</label>
                          <p class="text-gray-500">Get notified when an SSL certificate check failed.</p>
                          @error('state.notification_types.certificate_check_failed') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                      </div>
                      <div class="relative flex items-start">
                        <div class="flex h-5 items-center">
                          <input id="certificate_expires_soon" wire:model.defer="state.notification_types.certificate_expires_soon" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                        </div>
                        <div class="ml-3 text-sm">
                          <label for="certificate_expires_soon" class="font-medium text-gray-700">SSL Certificate Expires Soon</label>
                          <p class="text-gray-500">Get notified when an SSL certificate is expiring soon.</p>
                          @error('state.notification_types.certificate_expires_soon') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                      </div>
                      <div class="relative flex items-start">
                        <div class="flex h-5 items-center">
                          <input id="fetched_server_data_succeeded" wire:model.defer="state.notification_types.fetched_server_data_succeeded" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                        </div>
                        <div class="ml-3 text-sm">
                          <label for="fetched_server_data_succeeded" class="font-medium text-gray-700">Fetched Remote Server Data Succeeded</label>
                          <p class="text-gray-500">Get notified when fetching the remote server data succeeds.</p>
                          @error('state.notification_types.fetched_server_data_succeeded') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                      </div>
                      <div class="relative flex items-start">
                        <div class="flex h-5 items-center">
                          <input id="fetched_server_data_failed" wire:model.defer="state.notification_types.fetched_server_data_failed" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                        </div>
                        <div class="ml-3 text-sm">
                          <label for="fetched_server_data_failed" class="font-medium text-gray-700">Fetched Remote Server Data Failed</label>
                          <p class="text-gray-500">Get notified when fetching the remote server data failed.</p>
                          @error('state.notification_types.fetched_server_data_failed') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                      </div>
                      <div class="relative flex items-start">
                        <div class="flex h-5 items-center">
                          <input id="fetched_server_data_failed" wire:model.defer="state.notification_types.domain_name_expires_soon" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                        </div>
                        <div class="ml-3 text-sm">
                          <label for="fetched_server_data_failed" class="font-medium text-gray-700">Domain Name Expires Soon</label>
                          <p class="text-gray-500">Get notified when the domain name is expiring soon.</p>
                          @error('state.notification_types.domain_name_expires_soon') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="pt-5">
          <div class="flex justify-end">
            <a href="{{ route('users.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">Cancel</a>
            <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">Save</button>
          </div>
        </div>
      </form>

    </div>

    <!-- /End Content -->
  </div>
</div>
