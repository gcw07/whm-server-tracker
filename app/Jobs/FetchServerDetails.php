<?php

namespace App\Jobs;

use App\Connectors\ServerConnector;
use App\Events\FetchedServerDetails;
use App\Server;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class FetchServerDetails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var integer
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
     * @return boolean
     */
    public function handle(ServerConnector $serverConnector)
    {
        $serverConnector->setServer($this->server);

        tap($this->server->fetchers(), function ($fetchers) use ($serverConnector) {
            $fetchers->diskUsage($serverConnector);
            $fetchers->backup($serverConnector);
            $fetchers->phpVersion($serverConnector);
        });

        $this->server->update([
            'details_last_updated' => Carbon::now()
        ]);

        event(new FetchedServerDetails($this->server));

        return true;
    }

    /**
     * The job failed to process.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        if ($exception instanceof InvalidServerTypeException) {
//            Server type must be a vps or dedicated server.
        } elseif ($exception instanceof MissingTokenException) {
//            Server API token is missing.
        } elseif ($exception instanceof ServerConnectionException) {
//            Unable to connect to server. Try again later.
        } elseif ($exception instanceof ForbiddenAccessException) {
//            Access if forbidden on server. Check credentials.
        }
    }
}
