<?php

use App\Enums\ServerTypeEnum;
use App\Models\Account;
use App\Models\Server;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

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
    $this->server->settings()->set('disk_used', 100000);
    $this->server->settings()->set('disk_available', 200000);

    $this->assertCount(2, $this->server->settings);
    $this->assertEquals('100000', $this->server->settings()->get('disk_used'));
});

it('a server can get a setting', function () {
    $this->server->settings()->set('disk_available', 200000);

    $this->assertEquals('200000', $this->server->settings()->get('disk_available'));
});

it('a server can update a setting', function () {
    $this->server->settings()->set('disk_available', 200000);
    $this->assertEquals('200000', $this->server->settings()->get('disk_available'));

    $this->server->settings()->set('disk_available', 500000);
    $this->assertEquals('500000', $this->server->settings()->get('disk_available'));
});

it('a server can update multiple settings at once', function () {
    $this->server->settings()->merge([
        'disk_used' => 10000,
        'disk_available' => 200000,
    ]);

    $this->assertEquals('10000', $this->server->settings()->get('disk_used'));
    $this->assertEquals('200000', $this->server->settings()->get('disk_available'));
});

it('a server can remove a setting', function () {
    $this->server->settings()->set('disk_available', 200000);
    $this->assertCount(1, $this->server->settings);

    $this->server->settings()->forget('disk_available');
    $this->assertCount(0, $this->server->settings);
});

it('a server can remove all settings', function () {
    $this->server->settings()->set('disk_used', 10000);
    $this->server->settings()->set('disk_available', 200000);

    $this->assertCount(2, $this->server->settings);

    $this->server->settings()->forgetAll();

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
    $accounts = [
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

    $this->server->fetchers()->processAccounts($accounts);

    $this->assertCount(1, $this->server->fresh()->accounts);
});

it('it will update an account if it exists', function () {
    $account = Account::factory()->create([
        'server_id' => $this->server->id,
        'domain' => 'my-site.com',
        'user' => 'mysite',
    ]);

    $accounts = [
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

    $this->server->fetchers()->processAccounts($accounts);

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

    $accounts = [$validAccount, $skipAccount];

    $this->server->fetchers()->processAccounts($accounts);

    tap($this->server->fresh(), function ($server) {
        $this->assertCount(1, $server->accounts);
    });
});

it('it will remove accounts that no longer exists on server', function () {
    $account1 = Account::factory()->create([
        'server_id' => $this->server->id,
        'domain' => 'first-site.com',
        'user' => 'firstsite',
    ]);

    $account2 = Account::factory()->create([
        'server_id' => $this->server->id,
        'domain' => 'site-to-remove.com',
        'user' => 'sitetoremove',
    ]);

    $accounts = [
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

    $this->server->fetchers()->processAccounts($accounts);

    tap($this->server->fresh(), function ($server) {
        $this->assertCount(1, $server->accounts);
    });
});

it('will only remove accounts that no longer exists on the server that is being processed', function () {
    $serverB = Server::factory()->create();

    $accountToKeep = Account::factory()->create([
        'server_id' => $serverB->id,
        'domain' => 'site-to-remove.com',
        'user' => 'sitetoremove',
    ]);

    $account1 = Account::factory()->create([
        'server_id' => $this->server->id,
        'domain' => 'first-site.com',
        'user' => 'firstsite',
    ]);

    $account2 = Account::factory()->create([
        'server_id' => $this->server->id,
        'domain' => 'site-to-remove.com',
        'user' => 'sitetoremove',
    ]);

    $fetchedAccounts = [
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

    $this->server->fetchers()->processAccounts($fetchedAccounts);

    tap($this->server->fresh(), function ($server) {
        $this->assertCount(1, $server->accounts);
    });

    $this->assertCount(1, $serverB->fresh()->accounts);
    $this->assertEquals(2, Account::count());
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
    $serverA = Server::factory()->create();
    $serverA->settings()->set('disk_used', '16305616');

    $serverB = Server::factory()->create();
    $serverB->settings()->set('disk_used', '204800');

    $serverC = Server::factory()->create();
    $serverC->settings()->set('disk_used', '570');

    $this->assertEquals('15.55 GB', $serverA->formatted_disk_used);
    $this->assertEquals('200 MB', $serverB->formatted_disk_used);
    $this->assertEquals('570 KB', $serverC->formatted_disk_used);
});

it('can get formatted disk available', function () {
    $serverA = Server::factory()->create();
    $serverA->settings()->set('disk_available', '109523504');

    $serverB = Server::factory()->create();

    $this->assertEquals('104.45 GB', $serverA->formatted_disk_available);
    $this->assertEquals('Unknown', $serverB->formatted_disk_available);
});

it('can get formatted disk total', function () {
    $serverA = Server::factory()->create();
    $serverA->settings()->set('disk_total', '125829120');

    $serverB = Server::factory()->create();

    $this->assertEquals('120 GB', $serverA->formatted_disk_total);
    $this->assertEquals('Unknown', $serverB->formatted_disk_total);
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

it('can get whm external url', function () {
    $serverA = Server::factory()->make(['address' => '1.1.1.1', 'port' => 2087]);
    $serverB = Server::factory()->make(['address' => '3.3.3.3', 'port' => 2086]);

    $this->assertEquals('https://1.1.1.1:2087', $serverA->whm_url);
    $this->assertEquals('http://3.3.3.3:2086', $serverB->whm_url);
});

it('the api token should not be included in returned data', function () {
    $server = Server::factory()->make(['server_type' => ServerTypeEnum::Vps, 'token' => 'valid-token']);

    $this->assertArrayNotHasKey('token', $server->toArray());
});

it('can get formatted backup days', function () {
    $serverA = Server::factory()->create();
    $serverA->settings()->set('backup_days', '0,1,2');

    $serverB = Server::factory()->create();
    $serverB->settings()->set('backup_days', '3,4,5,6');

    $serverC = Server::factory()->create();
    $serverC->settings()->set('backup_days', '0,2,4,6');

    $serverD = Server::factory()->create();

    $this->assertEquals('Sun,Mon,Tue', $serverA->formatted_backup_days);
    $this->assertEquals('Wed,Thu,Fri,Sat', $serverB->formatted_backup_days);
    $this->assertEquals('Sun,Tue,Thu,Sat', $serverC->formatted_backup_days);
    $this->assertEquals('None', $serverD->formatted_backup_days);
});

it('can get formatted php version', function () {
    $serverA = Server::factory()->create();
    $serverA->settings()->set('php_version', 'ea-php54');

    $serverB = Server::factory()->create();
    $serverB->settings()->set('php_version', 'ea-php55');

    $serverC = Server::factory()->create();
    $serverC->settings()->set('php_version', 'ea-php56');

    $serverD = Server::factory()->create();
    $serverD->settings()->set('php_version', 'ea-php70');

    $serverE = Server::factory()->create();
    $serverE->settings()->set('php_version', 'ea-php71');

    $serverF = Server::factory()->create();
    $serverF->settings()->set('php_version', 'ea-php72');

    $serverG = Server::factory()->create();
    $serverG->settings()->set('php_version', 'ea-php73');

    $serverH = Server::factory()->create();
    $serverH->settings()->set('php_version', 'ea-php74');

    $serverI = Server::factory()->create();
    $serverI->settings()->set('php_version', 'ea-php80');

    $serverJ = Server::factory()->create();
    $serverJ->settings()->set('php_version', 'ea-php81');

    $serverK = Server::factory()->create();

    $this->assertEquals('PHP 5.4', $serverA->formatted_php_version);
    $this->assertEquals('PHP 5.5', $serverB->formatted_php_version);
    $this->assertEquals('PHP 5.6', $serverC->formatted_php_version);
    $this->assertEquals('PHP 7.0', $serverD->formatted_php_version);
    $this->assertEquals('PHP 7.1', $serverE->formatted_php_version);
    $this->assertEquals('PHP 7.2', $serverF->formatted_php_version);
    $this->assertEquals('PHP 7.3', $serverG->formatted_php_version);
    $this->assertEquals('PHP 7.4', $serverH->formatted_php_version);
    $this->assertEquals('PHP 8.0', $serverI->formatted_php_version);
    $this->assertEquals('PHP 8.1', $serverJ->formatted_php_version);
    $this->assertEquals('Unknown', $serverK->formatted_php_version);
});
