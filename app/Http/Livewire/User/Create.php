<?php

namespace App\Http\Livewire\User;

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
    ];

    protected $validationAttributes = [
        'state.name' => 'name',
        'state.email' => 'email',
        'state.password' => 'password',
        'state.password_confirmation' => 'password',
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
        ];
    }

    public function save(): \Illuminate\Http\RedirectResponse
    {
        $this->validate();

        $data = collect($this->state)->merge([
            'password' => bcrypt($this->state['password']),
        ])->except('password_confirmation')->toArray();

        User::create($data);

        return redirect()->route('users.index');
    }
}
