<?php

use App\Enums\ServerTypeEnum;
use App\Models\Account;
use App\Models\Server;
use App\Services\WHM\DataProcessors\ProcessAccounts;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\UptimeMonitor\Models\Monitor;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->server = Server::factory()->create();
});

it('a server has accounts', function () {
    $this->assertInstanceOf(
        'Illuminate\Database\Eloquent\Collection',
        $this->server->accounts
    );
});

it('a server can add a setting', function () {
    $this->server->settings['disk_used'] = 100000;
    $this->server->settings['disk_available'] = 200000;

    $this->assertCount(2, $this->server->settings);
    $this->assertEquals(100000, $this->server->settings['disk_used']);
});

it('a server can get a setting', function () {
    $this->server->settings['disk_available'] = 200000;

    $this->assertEquals(200000, $this->server->settings['disk_available']);
});

it('a server can update a setting', function () {
    $this->server->settings['disk_available'] = 200000;
    $this->assertEquals(200000, $this->server->settings['disk_available']);

    $this->server->settings['disk_available'] = 500000;
    $this->assertEquals(500000, $this->server->settings['disk_available']);
});

it('a setting will not overwrite all settings when a single one is specified', function () {
    $server = Server::factory()->create(['settings' => ['disk_available' => 500000]]);

    $this->assertEquals(500000, $server->settings['disk_available']);
    $server->settings['disk_used'] = 100000;

    $server->save();
    $server->refresh();

    $this->assertEquals(500000, $server->settings['disk_available']);
    $this->assertEquals(100000, $server->settings['disk_used']);
});

it('a server can update multiple settings at once', function () {
    $this->server->settings->merge([
        'disk_used' => 10000,
        'disk_available' => 200000,
    ]);

    $this->assertEquals(10000, $this->server->settings['disk_used']);
    $this->assertEquals(200000, $this->server->settings['disk_available']);
});

it('a server can remove a setting', function () {
    $this->server->settings['disk_available'] = 200000;
    $this->assertCount(1, $this->server->settings);

    $this->server->settings->forget('disk_available');
    $this->assertCount(0, $this->server->settings);
});

it('a server can remove all settings', function () {
    $this->server->settings['disk_used'] = 10000;
    $this->server->settings['disk_available'] = 200000;
    $this->assertCount(2, $this->server->settings);

    $this->server->settings->forgetAll();
    $this->assertCount(0, $this->server->settings);
});

it('a server can add an account', function () {
    $this->server->addAccount([
        'domain' => 'my-server-name.com',
        'user' => 'my-server',
        'ip' => '1.1.1.1',
        'backup' => true,
        'suspended' => false,
        'suspend_reason' => 'not suspended',
        'suspend_time' => null,
        'setup_date' => Carbon::parse('-1 month'),
        'disk_used' => '500M',
        'disk_limit' => '2000M',
        'plan' => '2 Gig',
    ]);

    $this->assertCount(1, $this->server->accounts);
});

it('a server can remove an account', function () {
    $account = Account::factory()->create([
        'server_id' => $this->server->id,
    ]);

    $this->server->removeAccount($account);

    $this->assertCount(0, $this->server->accounts);
});

it('it will add an account if it does not exist', function () {
    $data['data']['acct'] = [
        [
            'domain' => 'my-site.com',
            'user' => 'mysite',
            'ip' => '1.1.1.1',
            'backup' => 1,
            'suspended' => 0,
            'suspendreason' => 'not suspended',
            'suspendtime' => 0,
            'startdate' => '17 Jan 1 10:35',
            'diskused' => '300M',
            'disklimit' => '2000M',
            'plan' => '2 Gig',
        ],
    ];

    (new ProcessAccounts)->execute($this->server, $data);

    $this->assertCount(1, $this->server->fresh()->accounts);
});

it('it will update an account if it exists', function () {
    Account::factory()->create([
        'server_id' => $this->server->id,
        'domain' => 'my-site.com',
        'user' => 'mysite',
    ]);

    $data['data']['acct'] = [
        [
            'domain' => 'my-site.com',
            'user' => 'mysite',
            'ip' => '1.1.1.1',
            'backup' => 1,
            'suspended' => 0,
            'suspendreason' => 'not suspended',
            'suspendtime' => 0,
            'startdate' => '17 Jan 1 10:35',
            'diskused' => '300M',
            'disklimit' => '2000M',
            'plan' => '2 Gig',
        ],
    ];

    (new ProcessAccounts)->execute($this->server, $data);

    tap($this->server->fresh(), function ($server) {
        $this->assertCount(1, $server->accounts);
        $this->assertEquals('my-site.com', $server->accounts->first()->domain);
        $this->assertEquals('mysite', $server->accounts->first()->user);
    });
});

it('it will skip over ignored usernames', function () {
    $validAccount = [
        'domain' => 'my-site.com',
        'user' => 'mysite',
        'ip' => '1.1.1.1',
        'backup' => 1,
        'suspended' => 0,
        'suspendreason' => 'not suspended',
        'suspendtime' => 0,
        'startdate' => '17 Jan 1 10:35',
        'diskused' => '300M',
        'disklimit' => '2000M',
        'plan' => '2 Gig',
    ];

    $skipAccount = [
        'domain' => 'gwscripts.com',
        'user' => 'gwscripts',
        'ip' => '1.1.1.1',
        'backup' => 1,
        'suspended' => 0,
        'suspendreason' => 'not suspended',
        'suspendtime' => 0,
        'startdate' => '17 Jan 20 9:35',
        'diskused' => '300M',
        'disklimit' => '2000M',
        'plan' => '2 Gig',
    ];

    $data['data']['acct'] = [$validAccount, $skipAccount];

    (new ProcessAccounts)->execute($this->server, $data);

    tap($this->server->fresh(), function ($server) {
        $this->assertCount(1, $server->accounts);
    });
});

it('it will remove accounts that no longer exists on server', function () {
    Account::factory()->create([
        'server_id' => $this->server->id,
        'domain' => 'first-site.com',
        'user' => 'firstsite',
    ]);

    $account = Account::factory()->create([
        'server_id' => $this->server->id,
        'domain' => 'site-to-remove.com',
        'user' => 'sitetoremove',
    ]);

    Monitor::create([
        'url' => $account->domain_url,
        'uptime_check_enabled' => true,
        'certificate_check_enabled' => true,
    ]);

    $data['data']['acct'] = [
        [
            'domain' => 'first-site.com',
            'user' => 'firstsite',
            'ip' => '1.1.1.1',
            'backup' => 1,
            'suspended' => 0,
            'suspendreason' => 'not suspended',
            'suspendtime' => 0,
            'startdate' => '17 Jan 1 10:35',
            'diskused' => '300M',
            'disklimit' => '2000M',
            'plan' => '2 Gig',
        ],
    ];

    (new ProcessAccounts)->execute($this->server, $data);

    tap($this->server->fresh(), function ($server) {
        $this->assertCount(1, $server->accounts);
    });
});

it('will only remove accounts that no longer exists on the server that is being processed', function () {
    $serverB = Server::factory()->create();

    // This account should be kept because it is on another server
    Account::factory()->create([
        'server_id' => $serverB->id,
        'domain' => 'site-to-remove.com',
        'user' => 'sitetoremove',
    ]);

    Account::factory()->create([
        'server_id' => $this->server->id,
        'domain' => 'first-site.com',
        'user' => 'firstsite',
    ]);

    $account = Account::factory()->create([
        'server_id' => $this->server->id,
        'domain' => 'site-to-remove.com',
        'user' => 'sitetoremove',
    ]);

    Monitor::create([
        'url' => $account->domain_url,
        'uptime_check_enabled' => true,
        'certificate_check_enabled' => true,
    ]);

    $data['data']['acct'] = [
        [
            'domain' => 'first-site.com',
            'user' => 'firstsite',
            'ip' => '1.1.1.1',
            'backup' => 1,
            'suspended' => 0,
            'suspendreason' => 'not suspended',
            'suspendtime' => 0,
            'startdate' => '17 Jan 1 10:35',
            'diskused' => '300M',
            'disklimit' => '2000M',
            'plan' => '2 Gig',
        ],
    ];

    $this->assertEquals(3, Account::count());

    (new ProcessAccounts)->execute($this->server, $data);

    tap($this->server->fresh(), function ($server) {
        $this->assertCount(1, $server->accounts);
    });

    $this->assertCount(1, $serverB->fresh()->accounts);
    $this->assertEquals(2, Account::count());
});

it('it will add a monitor when adding a new account', function () {
    $data['data']['acct'] = [
        [
            'domain' => 'my-site.com',
            'user' => 'mysite',
            'ip' => '1.1.1.1',
            'backup' => 1,
            'suspended' => 0,
            'suspendreason' => 'not suspended',
            'suspendtime' => 0,
            'startdate' => '17 Jan 1 10:35',
            'diskused' => '300M',
            'disklimit' => '2000M',
            'plan' => '2 Gig',
        ],
    ];

    (new ProcessAccounts)->execute($this->server, $data);

    $account = $this->server->fresh()->accounts->first();

    $this->assertDatabaseHas('monitors', [
        'url' => $account->domain_url,
    ]);
});

it('it will update a monitor when updating an account if the url has changed', function () {
    $monitor = Monitor::create([
        'url' => 'https://my-site.com',
        'uptime_check_enabled' => true,
        'certificate_check_enabled' => true,
    ]);

    $account = Account::factory()->create([
        'server_id' => $this->server->id,
        'domain' => 'my-site.com',
        'user' => 'mysite',
        'monitor_id' => $monitor->id,
    ]);

    $this->assertDatabaseHas('monitors', [
        'url' => 'https://my-site.com',
    ]);

    $data['data']['acct'] = [
        [
            'domain' => 'my-new-site.com',
            'user' => 'mysite',
            'ip' => '1.1.1.1',
            'backup' => 1,
            'suspended' => 0,
            'suspendreason' => 'not suspended',
            'suspendtime' => 0,
            'startdate' => '17 Jan 1 10:35',
            'diskused' => '300M',
            'disklimit' => '2000M',
            'plan' => '2 Gig',
        ],
    ];

    (new ProcessAccounts)->execute($this->server, $data);

    $this->assertDatabaseHas('monitors', [
        'url' => 'https://my-new-site.com',
    ]);

    $this->assertDatabaseMissing('monitors', [
        'url' => 'https://my-site.com',
    ]);
});

it('it will skip updating a monitor when updating an account if the url remains the same', function () {
    $monitorToCheck = Monitor::create([
        'url' => 'https://my-site.com',
        'uptime_check_enabled' => true,
        'certificate_check_enabled' => true,
    ]);

    $account = Account::factory()->create([
        'server_id' => $this->server->id,
        'domain' => 'my-site.com',
        'user' => 'mysite',
        'suspended' => false,
        'monitor_id' => $monitorToCheck->id,
    ]);

    tap($account->fresh()->monitor, function ($monitor) {
        $this->assertEquals('https://my-site.com', $monitor->url);
    });

    $data['data']['acct'] = [
        [
            'domain' => 'my-site.com',
            'user' => 'mysite',
            'ip' => '1.1.1.1',
            'backup' => 1,
            'suspended' => 0,
            'suspendreason' => 'not suspended',
            'suspendtime' => 0,
            'startdate' => '17 Jan 1 10:35',
            'diskused' => '300M',
            'disklimit' => '2000M',
            'plan' => '2 Gig',
        ],
    ];

    (new ProcessAccounts)->execute($this->server, $data);

    tap($account->fresh()->monitor, function ($monitor) use ($monitorToCheck) {
        $this->assertEquals('https://my-site.com', $monitor->url);
        $this->assertTrue($monitor->is($monitorToCheck));
        $this->assertEquals($monitorToCheck->created_at, $monitor->created_at);
    });
});

it('it will remove the monitor when an account is removed', function () {
    $monitor1 = Monitor::create([
        'url' => 'https://first-site.com',
        'uptime_check_enabled' => true,
        'certificate_check_enabled' => true,
    ]);

    Account::factory()->create([
        'server_id' => $this->server->id,
        'domain' => 'first-site.com',
        'user' => 'firstsite',
        'monitor_id' => $monitor1->id,
    ]);

    $monitor2 = Monitor::create([
        'url' => 'https://site-to-remove.com',
        'uptime_check_enabled' => true,
        'certificate_check_enabled' => true,
    ]);

    $account = Account::factory()->create([
        'server_id' => $this->server->id,
        'domain' => 'site-to-remove.com',
        'user' => 'sitetoremove',
        'monitor_id' => $monitor2->id,
    ]);

    $this->assertDatabaseHas('monitors', [
        'url' => 'https://site-to-remove.com',
    ]);

    $data['data']['acct'] = [
        [
            'domain' => 'first-site.com',
            'user' => 'firstsite',
            'ip' => '1.1.1.1',
            'backup' => 1,
            'suspended' => 0,
            'suspendreason' => 'not suspended',
            'suspendtime' => 0,
            'startdate' => '17 Jan 1 10:35',
            'diskused' => '300M',
            'disklimit' => '2000M',
            'plan' => '2 Gig',
        ],
    ];

    (new ProcessAccounts)->execute($this->server, $data);

    $this->assertDatabaseMissing('monitors', [
        'url' => 'https://site-to-remove.com',
    ]);
});

it('it will not add a monitor when adding a new account if the account is suspended', function () {
    $data['data']['acct'] = [
        [
            'domain' => 'my-site.com',
            'user' => 'mysite',
            'ip' => '1.1.1.1',
            'backup' => 1,
            'suspended' => 1,
            'suspendreason' => 'suspended',
            'suspendtime' => '22 Jan 5 10:35',
            'startdate' => '17 Jan 1 10:35',
            'diskused' => '300M',
            'disklimit' => '2000M',
            'plan' => '2 Gig',
        ],
    ];

    (new ProcessAccounts)->execute($this->server, $data);

    $account = $this->server->fresh()->accounts->first();

    $this->assertDatabaseMissing('monitors', [
        'url' => $account->domain_url,
    ]);
});

it('it will add a monitor when updating an account if the account is no longer suspended', function () {
    $account = Account::factory()->create([
        'server_id' => $this->server->id,
        'domain' => 'my-site.com',
        'user' => 'mysite',
        'suspended' => true,
    ]);

    $this->assertDatabaseMissing('monitors', [
        'url' => 'https://my-site.com',
    ]);

    $data['data']['acct'] = [
        [
            'domain' => 'my-site.com',
            'user' => 'mysite',
            'ip' => '1.1.1.1',
            'backup' => 1,
            'suspended' => 0,
            'suspendreason' => 'not suspended',
            'suspendtime' => 0,
            'startdate' => '17 Jan 1 10:35',
            'diskused' => '300M',
            'disklimit' => '2000M',
            'plan' => '2 Gig',
        ],
    ];

    (new ProcessAccounts)->execute($this->server, $data);

    $this->assertDatabaseHas('monitors', [
        'url' => 'https://my-site.com',
    ]);
});

it('it will remove a monitor when updating an account if the account is suspended', function () {
    $monitor = Monitor::create([
        'url' => 'https://my-site.com',
        'uptime_check_enabled' => true,
        'certificate_check_enabled' => true,
    ]);

    $account = Account::factory()->create([
        'server_id' => $this->server->id,
        'domain' => 'my-site.com',
        'user' => 'mysite',
        'suspended' => false,
        'monitor_id' => $monitor->id,
    ]);

    $this->assertDatabaseHas('monitors', [
        'url' => 'https://my-site.com',
    ]);

    $data['data']['acct'] = [
        [
            'domain' => 'my-site.com',
            'user' => 'mysite',
            'ip' => '1.1.1.1',
            'backup' => 1,
            'suspended' => 1,
            'suspendreason' => 'suspended',
            'suspendtime' => '22 Jan 5 10:35',
            'startdate' => '17 Jan 1 10:35',
            'diskused' => '300M',
            'disklimit' => '2000M',
            'plan' => '2 Gig',
        ],
    ];

    (new ProcessAccounts)->execute($this->server, $data);

    $this->assertDatabaseMissing('monitors', [
        'url' => 'https://my-site.com',
    ]);
});

it('it will only add a monitor when adding a new account, if it does not already exist', function () {
    $serverB = Server::factory()->create();

    $account = Account::factory()->create([
        'server_id' => $serverB->id,
        'domain' => 'my-site.com',
        'user' => 'mysite',
    ]);

    Monitor::create([
        'url' => $account->domain_url,
        'uptime_check_enabled' => true,
        'certificate_check_enabled' => true,
    ]);

    $data['data']['acct'] = [
        [
            'domain' => 'my-site.com',
            'user' => 'mysite',
            'ip' => '1.1.1.1',
            'backup' => 1,
            'suspended' => 0,
            'suspendreason' => 'not suspended',
            'suspendtime' => 0,
            'startdate' => '17 Jan 1 10:35',
            'diskused' => '300M',
            'disklimit' => '2000M',
            'plan' => '2 Gig',
        ],
    ];

    (new ProcessAccounts)->execute($this->server, $data);

    $account = $this->server->fresh()->accounts->first();

    $this->assertDatabaseHas('monitors', [
        'url' => $account->domain_url,
    ]);
});

it('will only remove a monitor if the account no longer exists on any servers', function () {
    $serverB = Server::factory()->create();

    // This account should be kept because it is on another server
    Account::factory()->create([
        'server_id' => $serverB->id,
        'domain' => 'site-to-remove.com',
        'user' => 'sitetoremove',
    ]);

    Account::factory()->create([
        'server_id' => $this->server->id,
        'domain' => 'first-site.com',
        'user' => 'firstsite',
    ]);

    $account = Account::factory()->create([
        'server_id' => $this->server->id,
        'domain' => 'site-to-remove.com',
        'user' => 'sitetoremove',
    ]);

    Monitor::create([
        'url' => $account->domain_url,
        'uptime_check_enabled' => true,
        'certificate_check_enabled' => true,
    ]);

    $data['data']['acct'] = [
        [
            'domain' => 'first-site.com',
            'user' => 'firstsite',
            'ip' => '1.1.1.1',
            'backup' => 1,
            'suspended' => 0,
            'suspendreason' => 'not suspended',
            'suspendtime' => 0,
            'startdate' => '17 Jan 1 10:35',
            'diskused' => '300M',
            'disklimit' => '2000M',
            'plan' => '2 Gig',
        ],
    ];

    $this->assertEquals(3, Account::count());

    (new ProcessAccounts)->execute($this->server, $data);

    $this->assertDatabaseHas('monitors', [
        'url' => $account->domain_url,
    ]);
});

it('can get whm url', function () {
    $serverA = Server::factory()->make(['address' => '1.1.1.1', 'port' => 2087]);
    $serverB = Server::factory()->make(['address' => '3.3.3.3', 'port' => 2086]);

    $this->assertEquals('https://1.1.1.1:2087', $serverA->whm_url);
    $this->assertEquals('http://3.3.3.3:2086', $serverB->whm_url);
});

it('can get formatted server types', function () {
    $serverA = Server::factory()->make(['server_type' => ServerTypeEnum::Vps]);
    $serverB = Server::factory()->make(['server_type' => ServerTypeEnum::Dedicated]);
    $serverC = Server::factory()->make(['server_type' => ServerTypeEnum::Reseller]);

    $this->assertEquals('VPS', $serverA->formatted_server_type);
    $this->assertEquals('Dedicated', $serverB->formatted_server_type);
    $this->assertEquals('Reseller', $serverC->formatted_server_type);
});

it('can get formatted disk used', function () {
    $serverA = Server::factory()->create(['settings' => ['disk_used' => '16305616']]);
    $serverB = Server::factory()->create(['settings' => ['disk_used' => '204800']]);
    $serverC = Server::factory()->create(['settings' => ['disk_used' => '570']]);

    $this->assertEquals('15.55 GB', $serverA->formatted_disk_used);
    $this->assertEquals('200 MB', $serverB->formatted_disk_used);
    $this->assertEquals('570 KB', $serverC->formatted_disk_used);
});

it('can get formatted disk available', function () {
    $serverA = Server::factory()->create(['settings' => ['disk_available' => '109523504']]);
    $serverB = Server::factory()->create();

    $this->assertEquals('104.45 GB', $serverA->formatted_disk_available);
    $this->assertEquals('Unknown', $serverB->formatted_disk_available);
});

it('can get formatted disk total', function () {
    $serverA = Server::factory()->create(['settings' => ['disk_total' => '125829120']]);
    $serverB = Server::factory()->create();

    $this->assertEquals('120 GB', $serverA->formatted_disk_total);
    $this->assertEquals('Unknown', $serverB->formatted_disk_total);
});

it('can get formatted backup daily days', function () {
    $serverA = Server::factory()->create(['settings' => ['backup_daily_days' => '0,1,2']]);
    $serverB = Server::factory()->create(['settings' => ['backup_daily_days' => '3,4,5,6']]);
    $serverC = Server::factory()->create(['settings' => ['backup_daily_days' => '0,2,4,6']]);
    $serverD = Server::factory()->create();

    $this->assertEquals('Sun, Mon, Tue', $serverA->formatted_backup_daily_days);
    $this->assertEquals('Wed, Thu, Fri, Sat', $serverB->formatted_backup_daily_days);
    $this->assertEquals('Sun, Tue, Thu, Sat', $serverC->formatted_backup_daily_days);
    $this->assertEquals('None', $serverD->formatted_backup_daily_days);
});

it('can get formatted backup weekly day', function () {
    $serverA = Server::factory()->create(['settings' => ['backup_weekly_day' => '0']]);
    $serverB = Server::factory()->create(['settings' => ['backup_weekly_day' => '3']]);
    $serverC = Server::factory()->create(['settings' => ['backup_weekly_day' => '6']]);
    $serverD = Server::factory()->create();

    $this->assertEquals('Sunday', $serverA->formatted_backup_weekly_day);
    $this->assertEquals('Wednesday', $serverB->formatted_backup_weekly_day);
    $this->assertEquals('Saturday', $serverC->formatted_backup_weekly_day);
    $this->assertEquals('None', $serverD->formatted_backup_weekly_day);
});

it('can get formatted backup monthly days', function () {
    $serverA = Server::factory()->create(['settings' => ['backup_monthly_days' => '1,15']]);
    $serverB = Server::factory()->create(['settings' => ['backup_monthly_days' => '1']]);
    $serverC = Server::factory()->create(['settings' => ['backup_monthly_days' => '15']]);
    $serverD = Server::factory()->create();

    $this->assertEquals('1st, 15th', $serverA->formatted_backup_monthly_days);
    $this->assertEquals('1st', $serverB->formatted_backup_monthly_days);
    $this->assertEquals('15th', $serverC->formatted_backup_monthly_days);
    $this->assertEquals('None', $serverD->formatted_backup_monthly_days);
});

it('can get formatted php installed versions', function () {
    $serverA = Server::factory()->create(['settings' => ['php_installed_versions' => ['ea-php54']]]);
    $serverB = Server::factory()->create(['settings' => ['php_installed_versions' => ['ea-php55']]]);
    $serverC = Server::factory()->create(['settings' => ['php_installed_versions' => ['ea-php56']]]);
    $serverD = Server::factory()->create(['settings' => ['php_installed_versions' => ['ea-php70']]]);
    $serverE = Server::factory()->create(['settings' => ['php_installed_versions' => ['ea-php71']]]);
    $serverF = Server::factory()->create(['settings' => ['php_installed_versions' => ['ea-php72']]]);
    $serverG = Server::factory()->create(['settings' => ['php_installed_versions' => ['ea-php73']]]);
    $serverH = Server::factory()->create(['settings' => ['php_installed_versions' => ['ea-php74']]]);
    $serverI = Server::factory()->create(['settings' => ['php_installed_versions' => ['ea-php80']]]);
    $serverJ = Server::factory()->create(['settings' => ['php_installed_versions' => ['ea-php81']]]);
    $serverK = Server::factory()->create(['settings' => ['php_installed_versions' => ['ea-php74', 'ea-php80']]]);
    $serverL = Server::factory()->create();

    $this->assertEquals(['5.4'], $serverA->formatted_php_installed_versions);
    $this->assertEquals(['5.5'], $serverB->formatted_php_installed_versions);
    $this->assertEquals(['5.6'], $serverC->formatted_php_installed_versions);
    $this->assertEquals(['7.0'], $serverD->formatted_php_installed_versions);
    $this->assertEquals(['7.1'], $serverE->formatted_php_installed_versions);
    $this->assertEquals(['7.2'], $serverF->formatted_php_installed_versions);
    $this->assertEquals(['7.3'], $serverG->formatted_php_installed_versions);
    $this->assertEquals(['7.4'], $serverH->formatted_php_installed_versions);
    $this->assertEquals(['8.0'], $serverI->formatted_php_installed_versions);
    $this->assertEquals(['8.1'], $serverJ->formatted_php_installed_versions);
    $this->assertEquals(['7.4', '8.0'], $serverK->formatted_php_installed_versions);
    $this->assertEquals(['Unknown'], $serverL->formatted_php_installed_versions);
});

it('can get formatted php system version', function () {
    $serverA = Server::factory()->create(['settings' => ['php_system_version' => 'ea-php54']]);
    $serverB = Server::factory()->create(['settings' => ['php_system_version' => 'ea-php55']]);
    $serverC = Server::factory()->create(['settings' => ['php_system_version' => 'ea-php56']]);
    $serverD = Server::factory()->create(['settings' => ['php_system_version' => 'ea-php70']]);
    $serverE = Server::factory()->create(['settings' => ['php_system_version' => 'ea-php71']]);
    $serverF = Server::factory()->create(['settings' => ['php_system_version' => 'ea-php72']]);
    $serverG = Server::factory()->create(['settings' => ['php_system_version' => 'ea-php73']]);
    $serverH = Server::factory()->create(['settings' => ['php_system_version' => 'ea-php74']]);
    $serverI = Server::factory()->create(['settings' => ['php_system_version' => 'ea-php80']]);
    $serverJ = Server::factory()->create(['settings' => ['php_system_version' => 'ea-php81']]);
    $serverK = Server::factory()->create();

    $this->assertEquals('5.4', $serverA->formatted_php_system_version);
    $this->assertEquals('5.5', $serverB->formatted_php_system_version);
    $this->assertEquals('5.6', $serverC->formatted_php_system_version);
    $this->assertEquals('7.0', $serverD->formatted_php_system_version);
    $this->assertEquals('7.1', $serverE->formatted_php_system_version);
    $this->assertEquals('7.2', $serverF->formatted_php_system_version);
    $this->assertEquals('7.3', $serverG->formatted_php_system_version);
    $this->assertEquals('7.4', $serverH->formatted_php_system_version);
    $this->assertEquals('8.0', $serverI->formatted_php_system_version);
    $this->assertEquals('8.1', $serverJ->formatted_php_system_version);
    $this->assertEquals('Unknown', $serverK->formatted_php_system_version);
});

it('can get formatted whm version', function () {
    $serverA = Server::factory()->create(['settings' => ['whm_version' => '11.100.0.11']]);
    $serverB = Server::factory()->create(['settings' => ['whm_version' => '11.88.0.9999']]);
    $serverC = Server::factory()->create();

    $this->assertEquals('v100.0.11', $serverA->formatted_whm_version);
    $this->assertEquals('v88.0.9999', $serverB->formatted_whm_version);
    $this->assertEquals('Unknown', $serverC->formatted_whm_version);
});

it('can get if backups are enabled', function () {
    $serverA = Server::factory()->create(['settings' => ['backup_enabled' => true]]);
    $serverB = Server::factory()->create(['settings' => ['backup_enabled' => false]]);
    $serverC = Server::factory()->create();

    $this->assertTrue($serverA->backups_enabled);
    $this->assertFalse($serverB->backups_enabled);
    $this->assertFalse($serverC->backups_enabled);
});

it('can determine a missing api token', function () {
    $serverValidToken = Server::factory()->make(['server_type' => ServerTypeEnum::Vps, 'token' => 'valid-token']);
    $serverNoToken = Server::factory()->make(['server_type' => ServerTypeEnum::Vps]);

    $this->assertFalse($serverValidToken->missing_token);
    $this->assertTrue($serverNoToken->missing_token);
});

it('can determine if it can refresh external data', function () {
    $serverValidToken = Server::factory()->make(['server_type' => ServerTypeEnum::Vps, 'token' => 'valid-token']);
    $serverNoToken = Server::factory()->make(['server_type' => ServerTypeEnum::Vps]);
    $serverTypeNeedsNoToken = Server::factory()->make(['server_type' => ServerTypeEnum::Reseller]);

    $this->assertTrue($serverValidToken->can_refresh_data);
    $this->assertFalse($serverNoToken->can_refresh_data);
    $this->assertFalse($serverTypeNeedsNoToken->can_refresh_data);
});

it('the api token should not be included in returned data', function () {
    $server = Server::factory()->make(['server_type' => ServerTypeEnum::Vps, 'token' => 'valid-token']);

    $this->assertArrayNotHasKey('token', $server->toArray());
});
