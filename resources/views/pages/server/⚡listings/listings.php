<?php

use App\Livewire\WithCache;
use App\Models\Server;
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

    public ?string $filterBy = null;

    public function mount(): void
    {
        $this->filterBy = $this->getCache('servers', 'filterBy');
    }

    public function sort($column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function filterListingsBy($name): void
    {
        $this->filterBy = match ($name) {
            'no_backups' => 'no_backups',
            'outdated_php' => 'outdated_php',
            default => null,
        };

        $this->putCache('servers', 'filterBy', $this->filterBy);
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
                    return $query->where(function (Builder $query) {
                        $query
                            ->whereJsonContains('settings->php_installed_versions', 'ea-php54')
                            ->orWhereJsonContains('settings->php_installed_versions', 'ea-php55')
                            ->orWhereJsonContains('settings->php_installed_versions', 'ea-php56')
                            ->orWhereJsonContains('settings->php_installed_versions', 'ea-php70')
                            ->orWhereJsonContains('settings->php_installed_versions', 'ea-php71')
                            ->orWhereJsonContains('settings->php_installed_versions', 'ea-php72')
                            ->orWhereJsonContains('settings->php_installed_versions', 'ea-php73')
                            ->orWhereJsonContains('settings->php_installed_versions', 'ea-php74')
                            ->orWhereJsonContains('settings->php_installed_versions', 'ea-php80');
                    });
                }

                return $query;
            })
            ->paginate(50);
    }
};
