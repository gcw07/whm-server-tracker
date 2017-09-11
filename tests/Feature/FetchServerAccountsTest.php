<?php

namespace Tests\Feature;

use App\Connectors\FakeServerConnector;
use App\Connectors\ServerConnector;
use App\Jobs\FetchServerAccounts;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FetchServerAccountsTest extends TestCase
{
    use RefreshDatabase;

    private function validAccounts($times = 1, $extraAccounts = [])
    {
        $accounts = make('App\Account', [], $times);

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
    public function guests_can_not_fetch_server_accounts()
    {
        $server = create('App\Server');

        $response = $this->get("/servers/{$server->id}/fetch-accounts");

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function an_authorized_user_can_fetch_server_accounts()
    {
        $this->signIn();

        $server = create('App\Server', [
            'name'             => 'my-server-name',
            'address'          => '1.1.1.1',
            'port'             => 2087,
            'server_type'      => 'vps',
            'token'            => 'valid-api-token',
        ]);

        Queue::fake();

        $fake = new FakeServerConnector;
        $fake->setAccounts($this->validAccounts(2));

        $this->app->instance(ServerConnector::class, $fake);

        $response = $this->get("/servers/{$server->id}/fetch-accounts");

        $response->assertStatus(200);

        Queue::assertPushed(FetchServerAccounts::class, function ($job) use ($server) {
            return $job->server->is($server);
        });
    }
}
