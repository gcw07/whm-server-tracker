<?php

namespace App\Http\Livewire\DataTable;

trait WithSorting
{
    public $sortField;
    public $sortDirection;

    public function sortBy($field)
    {
        $this->sortDirection = $this->sortField === $field
            ? $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc'
            : 'asc';

        $this->sortField = $field;
    }

    public function applySorting($query)
    {
        return is_null($this->sortField) ? $query : $query->orderBy($this->sortField, $this->sortDirection);
    }
}
