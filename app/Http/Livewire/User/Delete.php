<?php

namespace App\Http\Livewire\User;

use App\Models\User;
use LivewireUI\Modal\ModalComponent;
use Usernotnull\Toast\Concerns\WireToast;

class Delete extends ModalComponent
{
    use WireToast;

    public $user;

    public function mount(User $user)
    {
        $this->user = $user;
    }

    public function render()
    {
        return view('livewire.user.delete');
    }

    public static function modalMaxWidth(): string
    {
        return 'xl';
    }

    public function delete()
    {
        if ($this->user->id === auth()->user()->id) {
            toast()->danger('You may not delete yourself.')->push();

            $this->closeModal();

            return false;
        }

        toast()->success('The user was deleted successfully.')->pushOnNextPage();

        $this->user->delete();

        return redirect()->route('users.index');
    }
}
