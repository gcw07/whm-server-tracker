<div>
  <!-- Page Header -->
  <div class="pb-5 border-b border-gray-200 sm:flex sm:items-center sm:justify-between">
    <h3 class="text-2xl leading-6 font-medium text-gray-900">
      Servers
    </h3>
    <div class="mt-3 sm:mt-0 sm:ml-4">
    </div>
  </div>
  <!-- / End Page Header -->

  <div class="mt-6">
    <!-- Begin content -->

    <div class="shadow bg-white px-6 py-4 sm:rounded-lg">

      <form wire:submit="save" class="space-y-8 divide-y divide-gray-200">
        <div class="space-y-8 divide-y divide-gray-200 sm:space-y-5">
          <div>
            <div>
              <h3 class="text-lg leading-6 font-medium text-gray-900 mb-1">Server Information</h3>
            </div>
            <div class="space-y-6 sm:space-y-5">
              <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                <label for="name" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2"> Name </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                  <input type="text" wire:model.live="state.name" id="name" class="block max-w-lg w-full shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm border-gray-300 rounded-md">
                  @error('state.name') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
              </div>

              <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                <label for="address" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2"> Address </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                  <input type="text" wire:model.live="state.address" id="address" class="block max-w-lg w-full shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm border-gray-300 rounded-md">
                  @error('state.address') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
              </div>

              <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                <label for="port" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2"> Port </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                  <input type="text" wire:model.live="state.port" id="port" class="block max-w-lg w-full shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm border-gray-300 rounded-md">
                  @error('state.port') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
              </div>

              <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                <label for="server_type" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2"> Server Type </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                  <select wire:model.live="state.server_type" id="server_type" class="max-w-lg block focus:ring-sky-500 focus:border-sky-500 w-full shadow-sm sm:max-w-xs sm:text-sm border-gray-300 rounded-md">
                    <option :value="null">Select Type</option>
                    <option value="dedicated">Dedicated</option>
                    <option value="reseller">Reseller</option>
                    <option value="vps">VPS</option>
                  </select>
                  @error('state.server_type') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
              </div>

              <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                <label for="notes" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2"> Notes </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                  <textarea wire:model.live="state.notes" id="notes" rows="3" class="max-w-lg shadow-sm block w-full focus:ring-sky-500 focus:border-sky-500 sm:text-sm border border-gray-300 rounded-md"></textarea>
                  @error('state.notes') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="pt-5">
          <div class="flex justify-end">
            <a href="{{ route('servers.show', $server) }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">Cancel</a>
            <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">Save</button>
          </div>
        </div>
      </form>

    </div>

    <!-- /End Content -->
  </div>
</div>
