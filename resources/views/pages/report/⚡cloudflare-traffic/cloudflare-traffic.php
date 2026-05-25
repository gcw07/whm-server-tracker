<?php

use App\Models\Monitor;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Session;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Cloudflare Traffic Report')] class extends Component
{
    use WithPagination;

    #[Session]
    public string $cfSortBy = 'visitors_30d';

    #[Session]
    public string $cfSortDirection = 'desc';

    public function sort(string $column): void
    {
        $allowedColumns = ['monitors.url', 'visitors_30d', 'requests_30d', 'bandwidth_30d'];

        if (! in_array($column, $allowedColumns)) {
            return;
        }

        if ($this->cfSortBy === $column) {
            $this->cfSortDirection = $this->cfSortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->cfSortBy = $column;
            $this->cfSortDirection = $column === 'monitors.url' ? 'asc' : 'desc';
        }

        $this->resetPage();
    }

    #[Computed]
    public function monitors()
    {
        $windowStart = today()->subDays(29);

        return Monitor::query()
            ->join('monitor_cloudflare_checks', 'monitors.id', '=', 'monitor_cloudflare_checks.monitor_id')
            ->leftJoin('cloudflare_analytics', function ($join) use ($windowStart) {
                $join->on('monitor_cloudflare_checks.id', '=', 'cloudflare_analytics.monitor_cloudflare_check_id')
                    ->where('cloudflare_analytics.date', '>=', $windowStart);
            })
            ->where('monitor_cloudflare_checks.enabled', true)
            ->select(
                'monitors.*',
                'monitor_cloudflare_checks.last_synced_at as cf_last_synced_at',
            )
            ->selectRaw('COALESCE(SUM(cloudflare_analytics.unique_visitors), 0) as visitors_30d')
            ->selectRaw('COALESCE(SUM(cloudflare_analytics.requests_total), 0) as requests_30d')
            ->selectRaw('COALESCE(SUM(cloudflare_analytics.bandwidth_total), 0) as bandwidth_30d')
            ->groupBy('monitors.id', 'monitor_cloudflare_checks.last_synced_at')
            ->orderBy($this->cfSortBy, $this->cfSortDirection)
            ->paginate(50);
    }

    public function formatBytes(int $bytes): string
    {
        if ($bytes >= 1_073_741_824) {
            return number_format($bytes / 1_073_741_824, 2).' GB';
        }

        if ($bytes >= 1_048_576) {
            return number_format($bytes / 1_048_576, 1).' MB';
        }

        if ($bytes >= 1_024) {
            return number_format($bytes / 1_024, 1).' KB';
        }

        return $bytes.' B';
    }
};
