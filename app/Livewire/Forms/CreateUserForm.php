<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Form;

class CreateUserForm extends Form
{
    public string $name = '';

    public string $email = '';

    public $password = '';

    public $password_confirmation = '';

    public $notification_types = [];

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', Password::defaults()],
            'notification_types' => ['array'],
            'notification_types.*' => Rule::in([
                'uptime_check_succeeded',
                'uptime_check_failed',
                'uptime_check_recovered',
                'certificate_check_succeeded',
                'certificate_check_failed',
                'certificate_expires_soon',
                'fetched_server_data_succeeded',
                'fetched_server_data_failed',
                'domain_name_expires_soon',
            ]),
        ];
    }

    public function store()
    {
        $data = $this->validate();

        $notificationTypes = [
            'uptime_check_succeeded' => in_array('uptime_check_succeeded', $this->notification_types) ?? false,
            'uptime_check_failed' => in_array('uptime_check_failed', $this->notification_types) ?? false,
            'uptime_check_recovered' => in_array('uptime_check_recovered', $this->notification_types) ?? false,
            'certificate_check_succeeded' => in_array('certificate_check_succeeded', $this->notification_types) ?? false,
            'certificate_check_failed' => in_array('certificate_check_failed', $this->notification_types) ?? false,
            'certificate_expires_soon' => in_array('certificate_expires_soon', $this->notification_types) ?? false,
            'fetched_server_data_succeeded' => in_array('fetched_server_data_succeeded', $this->notification_types) ?? false,
            'fetched_server_data_failed' => in_array('fetched_server_data_failed', $this->notification_types) ?? false,
            'domain_name_expires_soon' => in_array('domain_name_expires_soon', $this->notification_types) ?? false,
        ];

        $data = collect($data)
            ->merge([
                'password' => bcrypt($this->password),
                'notification_types' => $notificationTypes,
            ])
            ->except('password_confirmation')
            ->toArray();

        return User::create($data);
    }
}
