<?php

namespace App\Filters;

class ServerFilters extends Filters
{
    /**
     * Registered filters to operate upon.
     *
     * @var array
     */
    protected $filters = ['type'];

    /**
     * Filter the query by a given server type.
     *
     * @param string $serverType
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function type($serverType)
    {
        return $this->builder->where('server_type', $serverType);
    }
}
