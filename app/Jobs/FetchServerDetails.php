<?php

namespace App\Jobs;

use App\Connectors\ServerConnector;
use App\Server;
use Carbon\Carbon;
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

        $this->server->fetchDiskUsageDetails($serverConnector);
        $this->server->fetchBackupDetails($serverConnector);
        $this->server->fetchPhpVersion($serverConnector);

        $this->server->update([
            'details_last_updated' => Carbon::now()
        ]);

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
//        catch (InvalidServerTypeException $exception) {
//            return response()->json(['message' => 'Server type must be a vps or dedicated server.'], 422);
//        } catch (MissingTokenException $exception) {
//            return response()->json(['message' => 'Server API token is missing.'], 422);
//        } catch (ServerConnectionException $exception) {
//            return response()->json(['message' => 'Unable to connect to server. Try again later.'], 422);
//        } catch (ForbiddenAccessException $exception) {
//            return response()->json(['message' => 'Access if forbidden on server. Check credentials.'], 422);
//        }
    }
}
