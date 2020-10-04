<?php

namespace App\Listeners;

use App\Events\FetchedServerAccounts;

class ServerAccountsUpdated
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
     * @param  FetchedServerAccounts  $event
     * @return void
     */
    public function handle(FetchedServerAccounts $event)
    {
        //
    }
}
