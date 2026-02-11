<?php

use App\Livewire\Forms\EditUserForm;
use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Edit User')] class extends Component
{
    public EditUserForm $form;

    public function mount(User $user): void
    {
        $this->form->setUser($user);
    }

    public function save(): void
    {
        $this->form->store();

        Flux::toast(
            text: 'The user information was updated successfully.',
            heading: 'Updated...',
            variant: 'success',
        );

        $this->redirectRoute('users.index', [],true, true);
    }
};
