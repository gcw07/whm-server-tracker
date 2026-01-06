<?php

namespace App\Livewire\User;

use App\Models\User;
use LivewireUI\Modal\ModalComponent;
use Usernotnull\Toast\Concerns\WireToast;

class Delete extends ModalComponent
{
    use WireToast;

    public $user;

    public function mount(User $user)
    {
        abort_if(auth()->guest(), 401);

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

    public function delete(): false|\Illuminate\Http\RedirectResponse
    {
        if ($this->user->id === auth()->user()->id) {
            toast()->danger('You may not delete yourself.')->push();

            $this->closeModal();

            return false;
        }

        $this->user->delete();

        toast()->success('The user was deleted successfully.')->pushOnNextPage();

        return to_route('users.index');
    }
}
