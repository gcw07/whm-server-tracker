<div class="px-4 pt-5 pb-4">
  <div class="hidden sm:block absolute top-0 right-0 pt-4 pr-4">
    <button wire:click="cancel" type="button" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
      <span class="sr-only">Close</span>
      <x-heroicon-o-x-mark class="h-6 w-6" />
    </button>
  </div>
  <form wire:submit.prevent="save">
    <div>
      <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">New API Token</h3>
        <div class="mt-2">

          <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:pt-5">
            <label for="token" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2"> API Token </label>
            <div class="mt-1 sm:mt-0 sm:col-span-2">
              <input type="text" wire:model="state.token" id="token" class="block max-w-lg w-full shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm border-gray-300 rounded-md">
              @error('state.token') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
            </div>
          </div>

        </div>
      </div>
    </div>
    <div class="mt-8 sm:mt-4 sm:flex sm:flex-row-reverse">
      <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-sky-600 text-base font-medium text-white hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 sm:ml-3 sm:w-auto sm:text-sm">Save</button>
      <button wire:click="cancel" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
    </div>
  </form>
</div>
