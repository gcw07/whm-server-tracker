<?php

namespace Tests\Unit\Jobs;

use App\Connectors\FakeServerConnector;
use App\Connectors\ServerConnector;
use App\Jobs\FetchServerAccounts;
use App\Models\Server;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Factories\AccountFactory;
use Tests\Factories\ServerFactory;
use Tests\TestCase;

class FetchServerAccountsTest extends TestCase
{
    use RefreshDatabase;

    private function validAccounts($times = 1, $extraAccounts = [])
    {
        $accounts = AccountFactory::new()->times($times)->make();

        if (sizeof($extraAccounts) > 0) {
            return $accounts
                ->push($extraAccounts)
                ->map(function ($item) {
                    return [
                        'domain'        => $item->domain,
                        'user'          => $item->user,
                        'ip'            => $item->ip,
                        'backup'        => $item->backup,
                        'suspended'     => $item->suspended,
                        'suspendreason' => $item->suspend_reason,
                        'suspendtime'   => $item->suspend_time === null ? 0 : $item->suspend_time->timestamp,
                        'startdate'     => $item->setup_date->format('y M d G:i'),
                        'diskused'      => $item->disk_used,
                        'disklimit'     => $item->disk_limit,
                        'plan'          => $item->plan,
                    ];
                });
        }

        return $accounts
            ->map(function ($item) {
                return [
                    'domain'        => $item->domain,
                    'user'          => $item->user,
                    'ip'            => $item->ip,
                    'backup'        => $item->backup,
                    'suspended'     => $item->suspended,
                    'suspendreason' => $item->suspend_reason,
                    'suspendtime'   => $item->suspend_time === null ? 0 : $item->suspend_time->timestamp,
                    'startdate'     => $item->setup_date->format('y M d G:i'),
                    'diskused'      => $item->disk_used,
                    'disklimit'     => $item->disk_limit,
                    'plan'          => $item->plan,
                ];
            });
    }

    /** @test */
    public function it_fetches_server_accounts()
    {
        $server = ServerFactory::new()->create([
            'name'        => 'my-server-name',
            'address'     => '1.1.1.1',
            'port'        => 1000,
            'server_type' => 'vps',
            'token'       => 'valid-server-api-token',
        ]);

        $fake = new FakeServerConnector;
        $fake->setAccounts($this->validAccounts(2));
        $this->app->instance(ServerConnector::class, $fake);

        FetchServerAccounts::dispatch($server);

        tap($server->fresh(), function (Server $server) {
            $this->assertEquals('my-server-name', $server->name);

            $this->assertCount(2, $server->accounts);
        });
    }
}
