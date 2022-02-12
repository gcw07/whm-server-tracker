<?php

namespace App\Jobs;

use App\Connectors\ServerConnector;
use App\Events\FetchedServerAccounts;
use App\Models\Server;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchServerAccounts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * @var Server
     */
    public $server;

    /**
     * Create a new job instance.
     *
     * @param Server $server
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * Execute the job.
     *
     * @param ServerConnector $serverConnector
     * @return bool
     */
    public function handle(ServerConnector $serverConnector)
    {
        $serverConnector->setServer($this->server);

        $this->server->fetchers()->accounts($serverConnector);

        event(new FetchedServerAccounts($this->server));

        return true;
    }

    /**
     * The job failed to process.
     *
     * @param  \Exception $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        if ($exception instanceof InvalidServerTypeException) {
        } elseif ($exception instanceof MissingTokenException) {
        } elseif ($exception instanceof ServerConnectionException) {
        } elseif ($exception instanceof ForbiddenAccessException) {
        }
    }
}
