<div>
  <flux:modal.trigger name="site-search-modal" shortcut="cmd.k">
    <flux:input as="button" placeholder="Search..." icon="magnifying-glass" kbd="⌘K" class="hover:bg-gray-100" />
  </flux:modal.trigger>

  <flux:modal name="site-search-modal" variant="bare" class="w-full max-w-136 my-[12vh] max-h-screen overflow-y-hidden">
    <div class="mx-auto block max-w-xl transform overflow-hidden rounded-xl bg-white shadow-2xl outline-1 outline-black/5 dark:bg-gray-900 dark:-outline-offset-1 dark:outline-white/10">
      <div class="p-3">
        <flux:input wire:model.live.debounce.800ms="siteSearch" icon="magnifying-glass" placeholder="Search site" autofocus autocomplete="off" clearable class:input="focus:outline-none" />
      </div>

      <div class="block max-h-96 transform-gpu scroll-py-3 overflow-y-auto p-3">
        @if($this->servers->count() > 0)
          <flux:heading class="font-bold">Servers</flux:heading>

          <div class="grid grid-cols-1 gap-2 mt-4">
            @foreach ($this->servers as $server)
              <a href="{{ route('servers.show', $server) }}">
                <flux:card size="sm" class="hover:bg-zinc-100 dark:hover:bg-zinc-700">
                  <flux:heading class="flex items-center gap-2">
                    {{ $server->name }}
                    <flux:icon name="arrow-turn-down-left" class="ml-auto text-zinc-400" variant="micro" />
                  </flux:heading>
                </flux:card>
              </a>
            @endforeach
          </div>
        @endif


      </div>
    </div>
  </flux:modal>
</div>
