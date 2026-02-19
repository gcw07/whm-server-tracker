<?php

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Users')] class extends Component
{
    #[Computed]
    public function users()
    {
        return User::query()
            ->withLastLogin()
            ->orderBy('name')
            ->paginate(50);
    }

    public function delete($id): void
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->user()->id) {
            Flux::toast(
                text: 'You may not delete yourself.',
                heading: 'Warning...',
                variant: 'warning',
            );

            $this->modal("delete-user-modal-$id")->close();

            return;
        }

        $user->delete();

        $this->modal("delete-user-modal-$id")->close();

        Flux::toast(
            text: 'The user was deleted successfully.',
            heading: 'Deleted...',
            variant: 'success',
        );

        $this->redirectRoute('users.index', [], true, true);
    }
};
