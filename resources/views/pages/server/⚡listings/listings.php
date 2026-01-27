<?php

use App\Livewire\WithCache;
use App\Models\Server;
use App\Services\PhpVersions;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Session;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Servers')] class extends Component
{
    use WithCache, WithPagination;

    #[Session]
    public string $serverType = 'all';

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

    public function filter($name): void
    {
        $this->filterBy = $name;
    }

    #[Computed]
    public function servers()
    {
        return Server::query()
            ->withCount(['accounts'])
            ->when($this->serverType !== 'all', function (Builder $query) {
                return $query->where('server_type', $this->serverType);
            })
            ->when($this->sortBy, function (Builder $query) {
                if ($this->sortBy === 'newest') {
                    return $query->orderBy('created_at', $this->sortDirection);
                }

                if ($this->sortBy === 'accounts') {
                    return $query->orderBy('accounts_count', $this->sortDirection);
                }

                if ($this->sortBy === 'usage') {
                    $direction = $this->sortDirection === 'asc' ? 'asc' : 'desc';
                    return $query->orderByRaw("CAST(json_unquote(json_extract(`settings`, '$.\"disk_percentage\"')) AS FLOAT) $direction");
                }

                return $query->orderBy('name', $this->sortDirection);
            })
            ->when($this->filterBy, function (Builder $query) {
                if ($this->filterBy === 'no_backups') {
                    return $query->where('settings->backup_enabled', false);
                }

                if ($this->filterBy === 'outdated_php') {
                    $phpVersions = PhpVersions::outdated('version');

                    return $query->where(function (Builder $query) use ($phpVersions) {
                        foreach ($phpVersions as $key => $version) {
                            $query->orWhereJsonContains('settings->php_installed_versions', "ea-$key");
                        }
                    });
                }

                return $query;
            })
            ->paginate(50);
    }
};
