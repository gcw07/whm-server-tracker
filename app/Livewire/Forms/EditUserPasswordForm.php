<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Illuminate\Validation\Rules\Password;
use Livewire\Form;

class EditUserPasswordForm extends Form
{
    public ?User $user;

    public $password = '';

    public $password_confirmation = '';

    protected function rules(): array
    {
        return [
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function store()
    {
        $this->validate();

        return $this->user->update([
            'password' => bcrypt($this->password),
        ]);
    }
}
