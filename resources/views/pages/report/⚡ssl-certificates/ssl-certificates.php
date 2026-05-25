<?php

use App\Models\AccountSslCertificate;
use App\Models\Monitor;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Session;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('SSL Certificates Report')] class extends Component
{
    use WithPagination;

    #[Session]
    public string $monitorSortBy = 'certificate_expiration_date';

    #[Session]
    public string $monitorSortDirection = 'asc';

    public function sortMonitors(string $column): void
    {
        $allowedColumns = ['url', 'certificate_expiration_date'];

        if (! in_array($column, $allowedColumns)) {
            return;
        }

        if ($this->monitorSortBy === $column) {
            $this->monitorSortDirection = $this->monitorSortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->monitorSortBy = $column;
            $this->monitorSortDirection = 'asc';
        }

        $this->resetPage('monitor_page');
    }

    #[Computed]
    public function monitorCertificates()
    {
        $sortBy = in_array($this->monitorSortBy, ['url', 'certificate_expiration_date'])
            ? $this->monitorSortBy
            : 'certificate_expiration_date';

        return Monitor::query()
            ->where('certificate_check_enabled', true)
            ->whereNotNull('certificate_expiration_date')
            ->orderBy($sortBy, $this->monitorSortDirection)
            ->paginate(50, ['*'], 'monitor_page');
    }

    #[Computed]
    public function accountCertificates()
    {
        return AccountSslCertificate::query()
            ->with(['account.server'])
            ->whereNotNull('expires_at')
            ->orderBy('expires_at', 'asc')
            ->paginate(50, ['*'], 'account_page');
    }
};
