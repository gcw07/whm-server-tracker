<?php

namespace App\Livewire\Server;

use App\Enums\ServerTypeEnum;
use App\Livewire\WithCache;
use App\Models\Server;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class Listings extends Component
{
    use WithCache, WithPagination;

    public ?string $serverType = null;

    public ?string $sortBy = null;

    public ?string $filterBy = null;

    public function mount()
    {
        $this->serverType = $this->getCache('servers', 'serverType');
        $this->sortBy = $this->getCache('servers', 'sortBy');
        $this->filterBy = $this->getCache('servers', 'filterBy');
    }

    public function render()
    {
        return view('livewire.server.listings', [
            'servers' => $this->query(),
        ])->layoutData(['title' => 'Servers']);
    }

    public function filterType($type): void
    {
        if (! is_null($type) && ServerTypeEnum::tryFrom($type)) {
            $this->serverType = $type;
        } else {
            $this->serverType = null;
        }

        $this->putCache('servers', 'serverType', $this->serverType);
    }

    public function updatedServerType($type): void
    {
        if (! is_null($type) && ServerTypeEnum::tryFrom($type)) {
            $this->serverType = $type;
        } else {
            $this->serverType = null;
        }

        $this->putCache('servers', 'serverType', $this->serverType);
    }

    public function sortListingsBy($name): void
    {
        $this->sortBy = match ($name) {
            'newest' => 'newest',
            'accounts' => 'accounts',
            'usage_high' => 'usage_high',
            'usage_low' => 'usage_low',
            default => null,
        };

        $this->putCache('servers', 'sortBy', $this->sortBy);
    }

    public function filterListingsBy($name)
    {
        $this->filterBy = match ($name) {
            'no_backups' => 'no_backups',
            'outdated_php' => 'outdated_php',
            default => null,
        };

        $this->putCache('servers', 'filterBy', $this->filterBy);
    }

    protected function query()
    {
        return Server::query()
            ->withCount(['accounts'])
            ->when($this->serverType, function (Builder $query) {
                return $query->where('server_type', $this->serverType);
            })
            ->when($this->sortBy, function (Builder $query) {
                if ($this->sortBy === 'newest') {
                    return $query->orderBy('created_at', 'DESC');
                }

                if ($this->sortBy === 'accounts') {
                    return $query->orderBy('accounts_count', 'DESC');
                }

                if ($this->sortBy === 'usage_high') {
                    return $query->orderByRaw("CAST(json_unquote(json_extract(`settings`, '$.\"disk_percentage\"')) AS FLOAT) DESC");
                }

                // usage low
                return $query->orderByRaw("CAST(json_unquote(json_extract(`settings`, '$.\"disk_percentage\"')) AS FLOAT) ASC");
            }, function ($query) {
                return $query->orderBy('name');
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
                            ->orWhereJsonContains('settings->php_installed_versions', 'ea-php74');
                    });
                }

                return $query;
            })
            ->paginate(50);
    }
}
