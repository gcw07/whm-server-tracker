<?php

namespace App\Livewire\User;

use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Edit extends Component
{
    public User $user;

    /**
     * The component's state.
     */
    public array $state = [];

    protected $validationAttributes = [
        'state.name' => 'name',
        'state.email' => 'email',
        'state.notification_types' => 'notification types',
        'state.notification_types.uptime_check_failed' => 'notification type - uptime check failed',
        'state.notification_types.uptime_check_succeeded' => 'notification type - uptime check succeeded',
        'state.notification_types.uptime_check_recovered' => 'notification type - uptime check recovered',
        'state.notification_types.certificate_check_succeeded' => 'notification type - certificate check succeeded',
        'state.notification_types.certificate_check_failed' => 'notification type - certificate check failed',
        'state.notification_types.certificate_expires_soon' => 'notification type - certificate expires soon',
        'state.notification_types.fetched_server_data_succeeded' => 'notification type - fetched server data succeeded',
        'state.notification_types.fetched_server_data_failed' => 'notification type - fetched server data failed',
        'state.notification_types.domain_name_expires_soon' => 'notification type - domain name expires soon',
    ];

    public function mount(User $user): void
    {
        $this->user = $user;

        $this->state = $user->only(['name', 'email', 'notification_types']);
    }

    public function render()
    {
        return view('livewire.user.edit')->layoutData(['title' => 'Edit User']);
    }

    protected function rules(): array
    {
        return [
            'state.name' => ['required', 'string', 'max:255'],
            'state.email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user->id),
            ],
            'state.notification_types.uptime_check_failed' => ['required', 'boolean'],
            'state.notification_types.uptime_check_succeeded' => ['required', 'boolean'],
            'state.notification_types.uptime_check_recovered' => ['required', 'boolean'],
            'state.notification_types.certificate_check_succeeded' => ['required', 'boolean'],
            'state.notification_types.certificate_check_failed' => ['required', 'boolean'],
            'state.notification_types.certificate_expires_soon' => ['required', 'boolean'],
            'state.notification_types.fetched_server_data_succeeded' => ['required', 'boolean'],
            'state.notification_types.fetched_server_data_failed' => ['required', 'boolean'],
            'state.notification_types.domain_name_expires_soon' => ['required', 'boolean'],
        ];
    }

    public function save(): \Illuminate\Http\RedirectResponse
    {
        $this->validate();

        $this->user->update($this->state);

        return to_route('users.index');
    }
}
