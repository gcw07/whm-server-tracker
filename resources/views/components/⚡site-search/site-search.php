<?php

use App\Models\Account;
use App\Models\Monitor;
use App\Models\Server;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Session;
use Livewire\Component;

new class extends Component
{
    public ?string $siteSearch = null;

    #[Session]
    public array $recentSearches = [];

    #[Computed]
    public function servers(): \Illuminate\Database\Eloquent\Collection|Collection
    {
        if ($this->siteSearch) {
            return Server::query()->withCount(['accounts'])->search($this->siteSearch)->orderBy('name')->get();
        }

        return collect();
    }

    #[Computed]
    public function accounts(): \Illuminate\Database\Eloquent\Collection|Collection
    {
        if ($this->siteSearch) {
            return Account::query()->with(['server'])->search($this->siteSearch)->orderBy('domain')->get();
        }

        return collect();
    }

    #[Computed]
    public function monitors(): \Illuminate\Database\Eloquent\Collection|Collection
    {
        if ($this->siteSearch) {
            return Monitor::query()->search($this->siteSearch)->orderBy('url')->get();
        }

        return collect();
    }

    public function registerSearchTerm($type, $model): void
    {
        $term = match ($type) {
            'server' => [
                'type' => 'server',
                'id' => $model['id'],
                'name' => $model['name']
            ],
            'account' => [
                'type' => 'account',
                'id' => $model['id'],
                'name' => $model['domain'],
                'server' => $model['server']['name'],
                'suspended' => (bool) $model['suspended']
            ],
            'monitor' => [
                'type' => 'monitor',
                'id' => $model['id'],
                'name' => $model['domain_name']
            ]
        };

        array_unshift($this->recentSearches, $term);

        if (count($this->recentSearches) > 5) {
            array_pop($this->recentSearches);
        }

        $redirect = match ($type) {
            'server' => route('servers.show', $model['id']),
            'account' => route('accounts.show', $model['id']),
            'monitor' => route('monitors.show', $model['id']),
        };

        $this->redirect($redirect);
    }
};
