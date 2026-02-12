<?php

use App\Livewire\Forms\EditUserPasswordForm;
use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Change Password')] class extends Component
{
    public EditUserPasswordForm $form;

    public function mount(User $user): void
    {
        $this->form->setUser($user);
    }

    public function save(): void
    {
        $this->form->store();

        Flux::toast(
            text: 'The user password was updated successfully.',
            heading: 'Updated...',
            variant: 'success',
        );

        $this->redirectRoute('users.index', [],true, true);
    }
};
