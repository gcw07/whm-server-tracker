<?php

use App\Models\AccountSslCertificate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('SSL Certificates Report')] class extends Component
{
    use WithPagination;

    #[Computed]
    public function accountCertificates()
    {
        return AccountSslCertificate::query()
            ->with(['account.server'])
            ->whereNotNull('expires_at')
            ->whereHas('account', function ($query) {
                $query->where('suspended', false)
                    ->where(function ($query) {
                        $query->whereNull('monitor_id')
                            ->orWhereHas('monitor', fn ($query) => $query->where('certificate_check_enabled', true));
                    });
            })
            ->orderBy('expires_at', 'asc')
            ->paginate(50, ['*'], 'account_page');
    }
};
