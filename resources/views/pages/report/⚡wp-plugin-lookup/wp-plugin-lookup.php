<?php

use App\Models\Monitor;
use App\Models\MonitorWpPlugin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Session;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('WP Plugin Lookup')] class extends Component
{
    use WithPagination;

    #[Session]
    public ?string $selectedPlugin = null;

    #[Session]
    public string $view = 'has';

    public function switchView(string $view): void
    {
        $this->view = $view;
        $this->resetPage();
    }

    public function updatedSelectedPlugin(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function pluginOptions(): Collection
    {
        return MonitorWpPlugin::query()->distinct()->orderBy('name')->pluck('name');
    }

    #[Computed]
    public function sites()
    {
        if (! $this->selectedPlugin) {
            return null;
        }

        return Monitor::query()
            ->whereHas('wordpressCheck', fn (Builder $query) => $query->where('check_source', 'agent'))
            ->when(
                $this->view === 'has',
                fn (Builder $query) => $query
                    ->whereHas('wpPlugins', fn (Builder $query) => $query->where('name', $this->selectedPlugin))
                    ->with(['wpPlugins' => fn ($query) => $query->where('name', $this->selectedPlugin)]),
                fn (Builder $query) => $query
                    ->whereDoesntHave('wpPlugins', fn (Builder $query) => $query->where('name', $this->selectedPlugin))
            )
            ->orderBy('url')
            ->paginate(100);
    }
};
