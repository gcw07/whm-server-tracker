<?php

namespace App\Livewire\User;

use App\Models\User;

class Delete
{
    public $user;

    public function mount(User $user): void
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

    public function delete(): false|\Livewire\Features\SupportRedirects\Redirector|\Illuminate\Http\RedirectResponse
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
