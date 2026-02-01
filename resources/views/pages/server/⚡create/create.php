<?php

use App\Livewire\Forms\CreateServerForm;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Create Server')] class extends Component
{
    public CreateServerForm $form;

    public function save()
    {
        $server = $this->form->store();

        return to_route('servers.show', $server->id);
    }
};
