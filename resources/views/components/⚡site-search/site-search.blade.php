<div>
  <flux:modal.trigger name="site-search-modal" shortcut="cmd.k">
    <flux:input as="button" placeholder="Search..." icon="magnifying-glass" kbd="⌘K" class="hover:bg-gray-100" />
  </flux:modal.trigger>

  <flux:modal name="site-search-modal" variant="bare" class="w-full max-w-136 my-[12vh] max-h-screen overflow-y-hidden">
    <div class="mx-auto block max-w-xl transform overflow-hidden rounded-xl bg-white shadow-2xl outline-1 outline-black/5 dark:bg-gray-900 dark:-outline-offset-1 dark:outline-white/10">
      <div class="p-3">
        <flux:input wire:model.live.debounce.800ms="siteSearch" icon="magnifying-glass" placeholder="Search site" autofocus autocomplete="off" clearable class:input="focus:outline-none" />
      </div>

      <div class="block max-h-96 transform-gpu scroll-py-3 overflow-y-auto p-3 space-y-5">
        @if($siteSearch)
          <div>
            <a href="{{ route('search', ['search' => $siteSearch]) }}">
              <flux:card size="sm" class="border-none hover:bg-zinc-100 dark:hover:bg-zinc-700">
                <flux:heading class="flex items-center gap-2">
                  <div class="flex items-center gap-2.5">
                    <flux:icon name="magnifying-glass" class="ml-auto size-5 text-zinc-400" />
                    {{ $siteSearch }}
                  </div>
                  <div class="ml-auto flex items-center gap-2.5">
                    <flux:text>See full results</flux:text>
                  </div>
                </flux:heading>
              </flux:card>
            </a>
          </div>
        @endif

        @if($this->servers->count() > 0)
          <div>
            <flux:heading level="3" class="font-bold! text-zinc-600!">Servers</flux:heading>

            <div class="grid grid-cols-1 gap-2 mt-1">
              @foreach ($this->servers as $server)
                <a href="{{ route('servers.show', $server) }}">
                  <flux:card size="sm" class="hover:bg-zinc-100 dark:hover:bg-zinc-700">
                    <flux:heading class="flex items-center gap-2">
                      <div class="flex items-center gap-2.5">
                        <flux:icon name="server" class="ml-auto size-5 text-zinc-400" />
                        {{ $server->name }}
                      </div>
                      <flux:icon name="arrow-turn-down-left" class="ml-auto text-zinc-400" variant="micro" />
                    </flux:heading>
                  </flux:card>
                </a>
              @endforeach
            </div>
          </div>
        @endif

        @if($this->accounts->count() > 0)
          <div>
            <flux:heading level="3" class="font-bold! text-zinc-600!">Accounts</flux:heading>

            <div class="grid grid-cols-1 gap-2 mt-1">
              @foreach ($this->accounts as $account)
                <a href="{{ route('accounts.show', $account) }}">
                  <flux:card size="sm" @class(['hover:bg-zinc-100 dark:hover:bg-zinc-700', 'bg-blue-200' => $account->suspended])>
                    <flux:heading class="flex items-center gap-2">
                      <div class="flex items-center gap-2.5">
                        <flux:icon name="globe-alt" class="ml-auto size-5 text-zinc-400" />
                        <div>
                          <div>
                            {{ $account->domain }}
                          </div>
                          <div class="text-zinc-500 text-xs">
                            {{ $account->server->name }}
                          </div>
                        </div>
                        @if($account->suspended)
                          <flux:badge as="button" size="sm" color="blue" inset="top bottom" icon:trailing="information-circle">Suspended</flux:badge>
                        @endif
                      </div>
                      <flux:icon name="arrow-turn-down-left" class="ml-auto text-zinc-400" variant="micro" />
                    </flux:heading>
                  </flux:card>
                </a>
              @endforeach
            </div>
          </div>
        @endif

        @if($this->monitors->count() > 0)
          <div>
            <flux:heading level="3" class="font-bold! text-zinc-600!">Monitors</flux:heading>

            <div class="grid grid-cols-1 gap-2 mt-1">
              @foreach ($this->monitors as $monitor)
                <a href="{{ route('monitors.show', $monitor) }}">
                  <flux:card size="sm" class="hover:bg-zinc-100 dark:hover:bg-zinc-700">
                    <flux:heading class="flex items-center gap-2">
                      <div class="flex items-center gap-2.5">
                        <flux:icon name="sparkles" class="ml-auto size-5 text-zinc-400" />
                        {{ preg_replace("(^https?://)", "", $monitor->url ) }}
                      </div>
                      <flux:icon name="arrow-turn-down-left" class="ml-auto text-zinc-400" variant="micro" />
                    </flux:heading>
                  </flux:card>
                </a>
              @endforeach
            </div>
          </div>
        @endif
      </div>

      <div class="p-3">
        <!-- Footer -->
      </div>
    </div>
  </flux:modal>
</div>
