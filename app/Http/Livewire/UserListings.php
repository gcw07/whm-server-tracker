<?php

namespace App\Http\Livewire;

use App\Http\Livewire\DataTable\WithPerPagePagination;
use App\Http\Livewire\DataTable\WithSorting;
use App\Models\User;
use Livewire\Component;

class UserListings extends Component
{
    use WithPerPagePagination, WithSorting;

    protected $queryString = ['sortField', 'sortDirection'];

    public function getRowsQueryProperty()
    {
        $query = User::query()->withLastLogin();

        return $this->applySorting($query);
    }

    public function getRowsProperty()
    {
        return $this->applyPagination($this->rowsQuery);
    }

    public function render()
    {
        return view('livewire.user-listings', [
            'users' => $this->rows,
        ])->layout('components.layouts.app', ['title' => 'Users']);
    }
}
