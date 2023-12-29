<?php

namespace App\Livewire\Account;

use App\Models\Account;
use LivewireUI\Modal\ModalComponent;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Usernotnull\Toast\Concerns\WireToast;

class Export extends ModalComponent
{
    use WireToast;

    public $sortBy;

    /**
     * The component's state.
     */
    public array $state = [
        'domain' => true,
        'server' => true,
        'username' => true,
        'ip' => true,
        'backups' => true,
        'suspended' => true,
        'suspended_reason' => true,
        'suspended_time' => true,
        'setup_date' => true,
        'disk_used' => true,
        'disk_limit' => true,
        'disk_usage' => true,
        'plan' => true,
        'wordpress_version' => true,
    ];

    public function mount(?string $sortBy)
    {
        abort_if(auth()->guest(), 401);

        $this->sortBy = $sortBy;
    }

    public function render()
    {
        return view('livewire.account.export');
    }

    public static function modalMaxWidth(): string
    {
        return 'xl';
    }

    protected function rules(): array
    {
        return [
            'state.domain' => ['required', 'boolean'],
            'state.server' => ['required', 'boolean'],
            'state.username' => ['required', 'boolean'],
            'state.ip' => ['required', 'boolean'],
            'state.backups' => ['required', 'boolean'],
            'state.suspended' => ['required', 'boolean'],
            'state.suspended_reason' => ['required', 'boolean'],
            'state.suspended_time' => ['required', 'boolean'],
            'state.setup_date' => ['required', 'boolean'],
            'state.disk_used' => ['required', 'boolean'],
            'state.disk_limit' => ['required', 'boolean'],
            'state.disk_usage' => ['required', 'boolean'],
            'state.plan' => ['required', 'boolean'],
            'state.wordpress_version' => ['required', 'boolean'],
        ];
    }

    public function save()
    {
        $this->validate();

        toast()->success('The export will begin downloading shortly.')->push();
        $this->closeModal();

        $columns = collect($this->state)->filter()->keys()->all();

        return response()->streamDownload(function () use ($columns) {
            $accountsCsv = SimpleExcelWriter::streamDownload('php://output', 'csv');

            Account::with(['server'])
                ->select('*')
                ->selectRaw('(disk_used / disk_limit) * 100 as sort_disk_usage')
                ->when($this->sortBy, function ($query) {
                    if ($this->sortBy === 'newest') {
                        return $query->orderBy('created_at', 'DESC')->orderBy('domain');
                    }

                    if ($this->sortBy === 'usage_high') {
                        return $query->orderBy('sort_disk_usage', 'DESC');
                    }

                    // usage low
                    return $query->orderBy('sort_disk_usage', 'ASC');
                }, function ($query) {
                    return $query->orderBy('domain');
                })
                ->each(function (Account $account) use ($accountsCsv, $columns) {
                    $accountsCsv->addRow($account->export($columns));
                });

            $accountsCsv->close();
        }, 'accounts.csv');
    }

    public function cancel()
    {
        $this->closeModal();
    }
}
