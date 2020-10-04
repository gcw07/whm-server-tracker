<?php

namespace App\Listeners;

use App\Events\FetchedServerDetails;

class ServerDetailsUpdated
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  FetchedServerDetails  $event
     * @return void
     */
    public function handle(FetchedServerDetails $event)
    {
        //
    }
}
