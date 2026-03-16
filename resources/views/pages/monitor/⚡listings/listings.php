<?php

use App\Models\Monitor;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Session;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Monitors')] class extends Component
{
    use WithPagination;

    #[Session]
    public string $monitorType = 'all';

    #[Session]
    public string $sortBy = 'name';

    #[Session]
    public string $sortDirection = 'asc';

    #[Session]
    public string $filterBy = 'none';

    public function sort($column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function removeAllFilters(): void
    {
        $this->filterBy = 'none';
    }

    #[Computed]
    public function monitors()
    {
        return Monitor::query()
            ->with(['blacklistCheck', 'lighthouseCheck', 'domainCheck', 'accounts.sslCertificates'])
            ->when($this->monitorType !== 'all', fn (Builder $query) => $query->withIssues())
            ->when($this->sortBy, function (Builder $query) {
                return $query->orderBy('url', $this->sortDirection);
            })
            ->when($this->filterBy, function (Builder $query) {
                if ($this->filterBy === 'disabled') {
                    return $query->where(function ($query) {
                        $query->where('uptime_check_enabled', false)
                            ->orWhere('certificate_check_enabled', false)
                            ->orWhereHas('blacklistCheck', fn ($q) => $q->where('enabled', false))
                            ->orWhereHas('lighthouseCheck', fn ($q) => $q->where('enabled', false))
                            ->orWhereHas('domainCheck', fn ($q) => $q->where('enabled', false));
                    });
                }

                if ($this->filterBy === 'on_cloudflare') {
                    return $query->whereHas('domainCheck', fn ($q) => $q->where('is_on_cloudflare', true));
                }

                if ($this->filterBy === 'not_on_cloudflare') {
                    return $query->whereHas('domainCheck', fn ($q) => $q->where('is_on_cloudflare', false));
                }

                return $query;
            })
            ->paginate(50);
    }

    #[Computed]
    public function issuesCount()
    {
        return [
            'all' => Monitor::query()->count(),
            'issues' => Monitor::query()->withIssues()->count(),
        ];
    }
};
