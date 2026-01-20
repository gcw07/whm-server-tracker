<div class="px-4 pt-5 pb-4">
  <div class="hidden sm:block absolute top-0 right-0 pt-4 pr-4">
    <button wire:click="$dispatch('closeModal')" type="button" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
      <span class="sr-only">Close</span>
      <x-heroicon-o-x-mark class="h-6 w-6" />
    </button>
  </div>
  <div class="sm:flex sm:items-start">
    <div class="mx-auto shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
      <x-heroicon-o-exclamation-triangle class="h-6 w-6 text-red-600" />
    </div>
    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
      <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Reset API Token</h3>
      <div class="mt-2">
        <p class="text-sm text-gray-500">Are you sure you want to reset the api token for the server <span class="text-gray-800 font-semibold">"{{ $server->name }}"</span>? This action cannot be undone.</p>
      </div>
    </div>
  </div>
  <div class="mt-8 sm:mt-4 sm:flex sm:flex-row-reverse">
    <button wire:click="$dispatch('openModal', { component: 'server.new-token', arguments: { server: {{ $server->id }} }})" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">Confirm</button>
    <button wire:click="$dispatch('closeModal')" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
  </div>
</div>
