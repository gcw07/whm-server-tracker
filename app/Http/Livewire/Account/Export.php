<?php

namespace App\Http\Livewire\Account;

use App\Models\Server;
use LivewireUI\Modal\ModalComponent;
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
    ];

    public function mount(string|null $sortBy)
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
        ];
    }

    public function save()
    {
        $this->validate();



        toast()->success('The download will begin shortly.')->push();
        $this->closeModal();
    }

    public function cancel()
    {
        $this->closeModal();
    }
}
