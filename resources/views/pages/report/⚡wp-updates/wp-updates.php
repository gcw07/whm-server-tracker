<?php

use App\Models\Monitor;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Session;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('WP Updates Report')] class extends Component
{
    use WithPagination;

    #[Session]
    public string $sortBy = 'url';

    #[Session]
    public string $sortDirection = 'asc';

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    #[Computed]
    public function monitors()
    {
        return Monitor::query()
            ->with(['wordpressCheck'])
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(50);
    }
};
