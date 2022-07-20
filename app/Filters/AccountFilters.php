<?php

namespace App\Filters;

class AccountFilters extends Filters
{
    /**
     * Registered filters to operate upon.
     *
     * @var array
     */
    protected $filters = [];

    /**
     * Filter the query by a given server id.
     *
     * @param  int  $serverId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function server($serverId)
    {
        return $this->builder->where('server_id', $serverId);
    }
}
