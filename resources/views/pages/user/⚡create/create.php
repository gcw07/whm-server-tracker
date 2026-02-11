<?php

use App\Livewire\Forms\CreateUserForm;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Create User')] class extends Component
{
    public CreateUserForm $form;

    public function save()
    {
        $this->form->store();

        return to_route('users.index');
    }
};
