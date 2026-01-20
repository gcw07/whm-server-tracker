<?php

namespace App\Livewire\User;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class Create extends Component
{
    /**
     * The component's state.
     */
    public array $state = [
        'name' => '',
        'email' => '',
        'password' => '',
        'password_confirmation' => '',
        'notification_types' => [
            'uptime_check_failed' => false,
            'uptime_check_succeeded' => false,
            'uptime_check_recovered' => false,
            'certificate_check_succeeded' => false,
            'certificate_check_failed' => false,
            'certificate_expires_soon' => false,
            'fetched_server_data_succeeded' => false,
            'fetched_server_data_failed' => false,
            'domain_name_expires_soon' => false,
        ],
    ];

    protected $validationAttributes = [
        'state.name' => 'name',
        'state.email' => 'email',
        'state.password' => 'password',
        'state.password_confirmation' => 'password',
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

    public function render()
    {
        return view('livewire.user.create')->layoutData(['title' => 'Create User']);
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
                Rule::unique('users', 'email'),
            ],
            'state.password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->uncompromised(),
            ],
            'state.notification_types' => ['required', 'array'],
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

    public function save(): \Livewire\Features\SupportRedirects\Redirector | \Illuminate\Http\RedirectResponse
    {
        $this->validate();

        $data = collect($this->state)->merge([
            'password' => bcrypt($this->state['password']),
        ])->except('password_confirmation')->toArray();

        User::create($data);

        return to_route('users.index');
    }
}
