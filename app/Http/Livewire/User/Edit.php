<?php

namespace App\Http\Livewire\User;

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
    ];

    public function mount(User $user)
    {
        $this->user = $user;

        $this->state = $user->only(['name', 'email']);
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
        ];
    }

    public function save()
    {
        $this->validate();

        $this->user->update($this->state);

        return redirect()->route('users.index');
    }
}
