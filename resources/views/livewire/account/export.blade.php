<div class="px-4 pt-5 pb-4">
  <div class="hidden sm:block absolute top-0 right-0 pt-4 pr-4">
    <button wire:click="cancel" type="button" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
      <span class="sr-only">Close</span>
      <x-heroicon-o-x class="h-6 w-6" />
    </button>
  </div>
  <form wire:submit.prevent="save">
    <div>
      <div class="mt-3 text-center sm:mt-0 sm:mx-4 sm:text-left">
        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Export Accounts</h3>
        <div class="mt-6">
          <fieldset>
            <p>Select the columns you wish to include in this export.</p>
            <legend class="text-lg font-medium text-gray-900">Columns</legend>
            <div class="mt-4 border-t border-b border-gray-200 divide-y divide-gray-200">
              <div class="relative flex items-start py-4">
                <div class="min-w-0 flex-1 text-sm">
                  <label for="domain" class="font-medium text-gray-700 select-none">Domain</label>
                </div>
                <div class="ml-3 flex items-center h-5">
                  <input wire:model="state.domain" id="domain" type="checkbox" class="focus:ring-sky-500 h-4 w-4 text-sky-600 border-gray-300 rounded">
                  @error('state.domain') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
              </div>

              <div class="relative flex items-start py-4">
                <div class="min-w-0 flex-1 text-sm">
                  <label for="server" class="font-medium text-gray-700 select-none">Server</label>
                </div>
                <div class="ml-3 flex items-center h-5">
                  <input wire:model="state.server" id="server" type="checkbox" class="focus:ring-sky-500 h-4 w-4 text-sky-600 border-gray-300 rounded">
                  @error('state.server') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
              </div>

              <div class="relative flex items-start py-4">
                <div class="min-w-0 flex-1 text-sm">
                  <label for="username" class="font-medium text-gray-700 select-none">Username</label>
                </div>
                <div class="ml-3 flex items-center h-5">
                  <input wire:model="state.username" id="username" type="checkbox" class="focus:ring-sky-500 h-4 w-4 text-sky-600 border-gray-300 rounded">
                  @error('state.username') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
              </div>

              <div class="relative flex items-start py-4">
                <div class="min-w-0 flex-1 text-sm">
                  <label for="ip" class="font-medium text-gray-700 select-none">IP Address</label>
                </div>
                <div class="ml-3 flex items-center h-5">
                  <input wire:model="state.ip" id="ip" type="checkbox" class="focus:ring-sky-500 h-4 w-4 text-sky-600 border-gray-300 rounded">
                  @error('state.ip') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
              </div>

              <div class="relative flex items-start py-4">
                <div class="min-w-0 flex-1 text-sm">
                  <label for="backups" class="font-medium text-gray-700 select-none">Backups</label>
                </div>
                <div class="ml-3 flex items-center h-5">
                  <input wire:model="state.backups" id="backups" type="checkbox" class="focus:ring-sky-500 h-4 w-4 text-sky-600 border-gray-300 rounded">
                  @error('state.backups') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
              </div>

              <div class="relative flex items-start py-4">
                <div class="min-w-0 flex-1 text-sm">
                  <label for="suspended" class="font-medium text-gray-700 select-none">Suspended</label>
                </div>
                <div class="ml-3 flex items-center h-5">
                  <input wire:model="state.suspended" id="suspended" type="checkbox" class="focus:ring-sky-500 h-4 w-4 text-sky-600 border-gray-300 rounded">
                  @error('state.suspended') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
              </div>

              <div class="relative flex items-start py-4">
                <div class="min-w-0 flex-1 text-sm">
                  <label for="suspended_reason" class="font-medium text-gray-700 select-none">Suspended Reason</label>
                </div>
                <div class="ml-3 flex items-center h-5">
                  <input wire:model="state.suspended_reason" id="suspended_reason" type="checkbox" class="focus:ring-sky-500 h-4 w-4 text-sky-600 border-gray-300 rounded">
                  @error('state.suspended_reason') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
              </div>

              <div class="relative flex items-start py-4">
                <div class="min-w-0 flex-1 text-sm">
                  <label for="suspended_time" class="font-medium text-gray-700 select-none">Suspended Date</label>
                </div>
                <div class="ml-3 flex items-center h-5">
                  <input wire:model="state.suspended_time" id="suspended_time" type="checkbox" class="focus:ring-sky-500 h-4 w-4 text-sky-600 border-gray-300 rounded">
                  @error('state.suspended_time') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
              </div>

              <div class="relative flex items-start py-4">
                <div class="min-w-0 flex-1 text-sm">
                  <label for="setup_date" class="font-medium text-gray-700 select-none">Setup Date</label>
                </div>
                <div class="ml-3 flex items-center h-5">
                  <input wire:model="state.setup_date" id="setup_date" type="checkbox" class="focus:ring-sky-500 h-4 w-4 text-sky-600 border-gray-300 rounded">
                  @error('state.setup_date') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
              </div>

              <div class="relative flex items-start py-4">
                <div class="min-w-0 flex-1 text-sm">
                  <label for="disk_used" class="font-medium text-gray-700 select-none">Disk Used</label>
                </div>
                <div class="ml-3 flex items-center h-5">
                  <input wire:model="state.disk_used" id="disk_used" type="checkbox" class="focus:ring-sky-500 h-4 w-4 text-sky-600 border-gray-300 rounded">
                  @error('state.disk_used') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
              </div>

              <div class="relative flex items-start py-4">
                <div class="min-w-0 flex-1 text-sm">
                  <label for="disk_limit" class="font-medium text-gray-700 select-none">Disk Limit</label>
                </div>
                <div class="ml-3 flex items-center h-5">
                  <input wire:model="state.disk_limit" id="disk_limit" type="checkbox" class="focus:ring-sky-500 h-4 w-4 text-sky-600 border-gray-300 rounded">
                  @error('state.disk_limit') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
              </div>

              <div class="relative flex items-start py-4">
                <div class="min-w-0 flex-1 text-sm">
                  <label for="disk_usage" class="font-medium text-gray-700 select-none">Disk Usage</label>
                </div>
                <div class="ml-3 flex items-center h-5">
                  <input wire:model="state.disk_usage" id="disk_usage" type="checkbox" class="focus:ring-sky-500 h-4 w-4 text-sky-600 border-gray-300 rounded">
                  @error('state.disk_usage') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
              </div>

              <div class="relative flex items-start py-4">
                <div class="min-w-0 flex-1 text-sm">
                  <label for="plan" class="font-medium text-gray-700 select-none">Plan</label>
                </div>
                <div class="ml-3 flex items-center h-5">
                  <input wire:model="state.plan" id="plan" type="checkbox" class="focus:ring-sky-500 h-4 w-4 text-sky-600 border-gray-300 rounded">
                  @error('state.plan') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
              </div>

            </div>
          </fieldset>

        </div>
      </div>
    </div>
    <div class="mt-8 sm:mt-4 sm:flex sm:flex-row-reverse">
      <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-sky-600 text-base font-medium text-white hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 sm:ml-3 sm:w-auto sm:text-sm">Export</button>
      <button wire:click="cancel" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
    </div>
  </form>
</div>
