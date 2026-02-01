<?php

use App\Livewire\Forms\EditServerForm;
use App\Models\Server;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Edit Server')] class extends Component
{
    public EditServerForm $form;

    public function mount(Server $server): void
    {
        $this->form->setServer($server);
    }

    public function save(): void
    {
        $this->form->store();

        Flux::toast(
            text: 'The server information was updated successfully.',
            heading: 'Updated...',
            variant: 'success',
        );

        $this->redirectRoute('servers.show', $this->form->server->id,true, true);
    }
};
