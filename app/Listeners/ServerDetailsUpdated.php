<?php

namespace App\Listeners;

use App\Events\FetchedServerDetails;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

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
