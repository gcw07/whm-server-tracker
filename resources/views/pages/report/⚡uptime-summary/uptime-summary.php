<?php

use App\Models\Monitor;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Session;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Uptime Summary Report')] class extends Component
{
    use WithPagination;

    #[Session]
    public string $uptimeSortBy = 'downtime_30d';

    #[Session]
    public string $uptimeSortDirection = 'desc';

    public function sort(string $column): void
    {
        $allowedColumns = ['url', 'downtime_7d', 'downtime_30d'];

        if (! in_array($column, $allowedColumns)) {
            return;
        }

        if ($this->uptimeSortBy === $column) {
            $this->uptimeSortDirection = $this->uptimeSortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->uptimeSortBy = $column;
            $this->uptimeSortDirection = $column === 'url' ? 'asc' : 'desc';
        }

        $this->resetPage();
    }

    #[Computed]
    public function monitors()
    {
        $window7dStart = today()->subDays(6)->startOfDay();
        $window30dStart = today()->subDays(29)->startOfDay();
        $windowEnd = today()->endOfDay();

        return Monitor::query()
            ->where('uptime_check_enabled', true)
            ->with('firstActiveAccount.server')
            ->withSum(
                ['outages as downtime_7d' => fn ($q) => $q
                    ->where('started_at', '<', $windowEnd)
                    ->where('ended_at', '>', $window7dStart),
                ],
                'duration_seconds'
            )
            ->withSum(
                ['outages as downtime_30d' => fn ($q) => $q
                    ->where('started_at', '<', $windowEnd)
                    ->where('ended_at', '>', $window30dStart),
                ],
                'duration_seconds'
            )
            ->withCount(
                ['outages as outage_count_30d' => fn ($q) => $q
                    ->where('started_at', '<', $windowEnd)
                    ->where('ended_at', '>', $window30dStart),
                ]
            )
            ->orderBy($this->uptimeSortBy, $this->uptimeSortDirection)
            ->paginate(50);
    }

    public function uptimePercentage(int $downtimeSeconds, int $windowDays): float
    {
        $totalSeconds = $windowDays * 86400;

        return max(0, round((($totalSeconds - $downtimeSeconds) / $totalSeconds) * 100, 2));
    }
};
