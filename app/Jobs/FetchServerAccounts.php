<?php

namespace App\Jobs;

use App\Connectors\ServerConnector;
use App\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class FetchServerAccounts implements ShouldQueue
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

        $this->server->fetchAccounts($serverConnector);

        return true;
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
//        } catch (InvalidServerTypeException $e) {
//            return response()->json(['message' => 'Server type must be a vps or dedicated server.'], 422);
//        } catch (MissingTokenException $e) {
//            return response()->json(['message' => 'Server API token is missing.'], 422);
//        } catch (ServerConnectionException $e) {
//            return response()->json(['message' => 'Unable to connect to server. Try again later.'], 422);
//        } catch (ForbiddenAccessException $e) {
//            return response()->json(['message' => 'Access if forbidden on server. Check credentials.'], 422);
//        }
    }
}
