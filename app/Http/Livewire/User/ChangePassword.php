<?php

namespace App\Http\Livewire\User;

use App\Models\User;
use Illuminate\Validation\Rules\Password;
use LivewireUI\Modal\ModalComponent;
use Usernotnull\Toast\Concerns\WireToast;

class ChangePassword extends ModalComponent
{
    use WireToast;

    public $user;

    /**
     * The component's state.
     */
    public array $state = [
        'password' => '',
        'password_confirmation' => '',
    ];

    protected $validationAttributes = [
        'state.password' => 'password',
        'state.password_confirmation' => 'password',
    ];

    public function mount(User $user)
    {
        abort_if(auth()->guest(), 401);

        $this->user = $user;
    }

    public function render()
    {
        return view('livewire.user.change-password');
    }

    public static function modalMaxWidth(): string
    {
        return 'xl';
    }

    public static function closeModalOnClickAway(): bool
    {
        return false;
    }

    protected function rules(): array
    {
        return [
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

    public function save()
    {
        $this->validate();

        $data = collect($this->state)->merge([
            'password' => bcrypt($this->state['password']),
        ])->except('password_confirmation')->toArray();

        $this->user->update($data);

        toast()->success('The users password was updated successfully.')->push();
        $this->closeModal();
    }

    public function cancel()
    {
        $this->closeModal();
    }
}
