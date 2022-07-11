<?php

namespace App\Http\Livewire\User;

use App\Models\User;
use LivewireUI\Modal\ModalComponent;

class Delete extends ModalComponent
{
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
            return redirect()->route('users.index');
//            return response(['message' => 'You may not delete yourself.'], 422);
        }

//        $this->user->delete();

        return redirect()->route('users.index');
    }
}
