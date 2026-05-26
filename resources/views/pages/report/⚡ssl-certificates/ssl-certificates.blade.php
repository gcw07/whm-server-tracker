<div>
  <div class="pb-5">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
      <flux:link :href="route('reports.index')" variant="subtle">Reports</flux:link>
      <flux:icon.chevron-right class="size-4" />
      <span>SSL Certificates</span>
    </div>
    <h3 class="text-2xl leading-6 font-medium text-gray-900">
      SSL Certificates
    </h3>
    <p class="mt-1 text-sm text-gray-500">Expiry status for SSL certificates across all cPanel accounts.</p>
  </div>

  {{-- cPanel Account SSL Certificates --}}
  <div class="mt-6">
    <h4 class="text-base font-semibold text-gray-800 mb-3">cPanel Account Certificates</h4>
    <flux:card class="p-0 overflow-hidden bg-gray-50">
      <flux:table :paginate="$this->accountCertificates" pagination:scroll-to>
        <flux:table.columns>
          <flux:table.column class="px-6! bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">DOMAIN</flux:table.column>
          <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">ACCOUNT</flux:table.column>
          <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">SERVER</flux:table.column>
          <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">TYPE</flux:table.column>
          <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">EXPIRES</flux:table.column>
          <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">STATUS</flux:table.column>
          <flux:table.column class="bg-gray-50 font-medium text-gray-500! text-xs tracking-wide">ISSUER</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
          @forelse ($this->accountCertificates as $cert)
            @php
              $expiresAt = $cert->expires_at;
              $isExpired = $expiresAt->isPast();
              $daysRemaining = (int) now()->diffInDays($expiresAt);
            @endphp
            <flux:table.row :key="$cert->id" @class(['bg-gray-50' => $loop->even, 'bg-white' => $loop->odd])>
              <flux:table.cell class="px-6! py-5!">
                @if ($cert->account->monitor_id)
                  <flux:link variant="subtle" :href="route('monitors.show', $cert->account->monitor_id)">
                    {{ $cert->servername }}
                  </flux:link>
                @else
                  {{ $cert->servername }}
                @endif
              </flux:table.cell>

              <flux:table.cell class="whitespace-nowrap text-sm text-gray-700">
                <flux:link variant="subtle" :href="route('accounts.show', $cert->account_id)">
                  {{ $cert->account->domain }}
                </flux:link>
              </flux:table.cell>

              <flux:table.cell class="whitespace-nowrap text-sm text-gray-700">
                <flux:link variant="subtle" :href="route('servers.show', $cert->account->server_id)">
                  {{ $cert->account->server->name }}
                </flux:link>
              </flux:table.cell>

              <flux:table.cell class="whitespace-nowrap">
                <flux:badge size="sm" color="zinc">{{ $cert->type->value }}</flux:badge>
              </flux:table.cell>

              <flux:table.cell class="whitespace-nowrap text-sm text-gray-700">
                {{ $expiresAt->format('M j, Y') }}
              </flux:table.cell>

              <flux:table.cell class="whitespace-nowrap">
                @if ($isExpired)
                  <flux:badge size="sm" icon="x-circle" color="red">Expired</flux:badge>
                @elseif ($daysRemaining <= 30)
                  <flux:badge size="sm" icon="exclamation-triangle" color="red">{{ $daysRemaining }} days</flux:badge>
                @elseif ($daysRemaining <= 60)
                  <flux:badge size="sm" icon="clock" color="amber">{{ $daysRemaining }} days</flux:badge>
                @else
                  <flux:badge size="sm" icon="check" color="green">{{ $daysRemaining }} days</flux:badge>
                @endif
              </flux:table.cell>

              <flux:table.cell class="text-sm text-gray-600">
                {{ $cert->issuer ?? '—' }}
              </flux:table.cell>
            </flux:table.row>
          @empty
            <flux:table.row>
              <flux:table.cell colspan="7" class="py-8 whitespace-nowrap font-semibold text-zinc-700">
                <div class="text-center">
                  <div class="flex items-center justify-center">
                    <flux:icon.magnifying-glass class="size-12" />
                  </div>
                  <p class="text-lg mt-6">No cPanel SSL certificates found.</p>
                </div>
              </flux:table.cell>
            </flux:table.row>
          @endforelse
        </flux:table.rows>
      </flux:table>
    </flux:card>
  </div>
</div>
