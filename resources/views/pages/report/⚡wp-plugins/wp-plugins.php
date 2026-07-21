<?php

use App\Models\MonitorWpPlugin;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Session;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('WP Plugins Report')] class extends Component
{
    use WithPagination;

    #[Session]
    public string $pluginSortBy = 'site_count';

    #[Session]
    public string $pluginSortDirection = 'desc';

    public function sort(string $column): void
    {
        $allowedColumns = ['name', 'site_count'];

        if (! in_array($column, $allowedColumns)) {
            return;
        }

        if ($this->pluginSortBy === $column) {
            $this->pluginSortDirection = $this->pluginSortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->pluginSortBy = $column;
            $this->pluginSortDirection = $column === 'name' ? 'asc' : 'desc';
        }

        $this->resetPage();
    }

    #[Computed]
    public function pluginGroups()
    {
        return MonitorWpPlugin::query()
            ->where('update_available', true)
            ->select('name', DB::raw('COUNT(*) as site_count'))
            ->groupBy('name')
            ->orderBy($this->pluginSortBy, $this->pluginSortDirection)
            ->paginate(25);
    }

    #[Computed]
    public function monitorsByPlugin(): Collection
    {
        $names = $this->pluginGroups->pluck('name');

        return MonitorWpPlugin::query()
            ->with('monitor')
            ->where('update_available', true)
            ->whereIn('name', $names)
            ->orderBy('name')
            ->get()
            ->groupBy('name')
            ->map(fn (Collection $sites) => $sites->sortBy(
                fn (MonitorWpPlugin $plugin) => Str::lower(preg_replace('#^https?://#', '', $plugin->monitor->url))
            )->values());
    }
};
