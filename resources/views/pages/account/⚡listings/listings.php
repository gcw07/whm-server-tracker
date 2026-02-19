<?php

use App\Models\Account;
use App\Models\Server;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Session;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\SimpleExcel\SimpleExcelWriter;

new #[Title('Accounts')] class extends Component
{
    use WithPagination;

    #[Session]
    public string $sortBy = 'domain';

    #[Session]
    public string $sortDirection = 'asc';

    #[Session]
    public string $filterBy = 'none';

    public array $exportColumns = [];

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
    public function accounts()
    {
        return Account::query()
            ->with(['server'])
            ->select('*')
            ->selectRaw('(disk_used / disk_limit) * 100 as sort_disk_usage')
            ->when($this->sortBy, function ($query) {
                if ($this->sortBy === 'newest') {
                    return $query->orderBy('created_at', $this->sortDirection)->orderBy('domain');
                }

                if ($this->sortBy === 'usage') {
                    return $query->orderBy('sort_disk_usage', $this->sortDirection);
                }

                if ($this->sortBy === 'server') {
                    return $query->orderBy(Server::select('name')->whereColumn('servers.id', 'accounts.server_id'), $this->sortDirection);
                }

                return $query->orderBy('domain', $this->sortDirection);
            })
            ->when($this->filterBy, function ($query) {
                if ($this->filterBy === 'duplicates') {
                    return $query->whereExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('accounts', 'a')
                            ->whereColumn('a.domain', 'accounts.domain')
                            ->limit(1)
                            ->offset(1);
                    });
                }

                if ($this->filterBy === 'suspended') {
                    return $query->where('suspended', true);
                }

                if ($this->filterBy === 'noBackups') {
                    return $query->where('backup', false);
                }

                return $query;
            })
            ->paginate(50);
    }

    public function exportAccounts()
    {
        $selectedColumns = $this->validate([
            'exportColumns' => ['required', 'array'],
            'exportColumns.*' => Rule::in([
                'domain',
                'server',
                'username',
                'ip',
                'backups',
                'suspended',
                'suspended_reason',
                'suspended_time',
                'setup_date',
                'disk_used',
                'disk_limit',
                'disk_usage',
                'plan',
                'wordpress_version',
            ]),
        ]);

        Flux::toast(
            text: 'The export will begin downloading shortly.',
            heading: 'Downloading...',
            variant: 'success',
        );

        $this->modal('export-accounts-modal')->close();

        $columns = $selectedColumns['exportColumns'];

        return response()->streamDownload(function () use ($columns) {
            $accountsCsv = SimpleExcelWriter::streamDownload('php://output', 'csv');

            Account::query()
                ->with(['server'])
                ->select('*')
                ->selectRaw('(disk_used / disk_limit) * 100 as sort_disk_usage')
                ->when($this->sortBy, function ($query) {
                    if ($this->sortBy === 'newest') {
                        return $query->orderBy('created_at', $this->sortDirection)->orderBy('domain');
                    }

                    if ($this->sortBy === 'usage') {
                        return $query->orderBy('sort_disk_usage', $this->sortDirection);
                    }

                    if ($this->sortBy === 'server') {
                        return $query->orderBy(Server::select('name')->whereColumn('servers.id', 'accounts.server_id'), $this->sortDirection);
                    }

                    return $query->orderBy('domain', $this->sortDirection);
                })
                ->when($this->filterBy, function ($query) {
                    if ($this->filterBy === 'duplicates') {
                        return $query->whereExists(function ($query) {
                            $query->select(DB::raw(1))
                                ->from('accounts', 'a')
                                ->whereColumn('a.domain', 'accounts.domain')
                                ->limit(1)
                                ->offset(1);
                        });
                    }

                    if ($this->filterBy === 'suspended') {
                        return $query->where('suspended', true);
                    }

                    if ($this->filterBy === 'noBackups') {
                        return $query->where('backup', false);
                    }

                    return $query;
                })
                ->each(function (Account $account) use ($accountsCsv, $columns) {
                    $accountsCsv->addRow($account->export($columns));
                });

            $accountsCsv->close();
        }, 'accounts.csv');
    }
};
