<?php

use App\Models\Account;
use App\Models\Server;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(LazilyRefreshDatabase::class);

it('has a server', function () {
    $server = Server::factory()->create();
    $account = Account::factory()->create(['server_id' => $server->id]);

    $this->assertInstanceOf(Server::class, $account->server);
});

it('can get cpanel external url', function () {
    $account = Account::factory()->make(['domain' => 'mydomain.com']);

    $this->assertEquals('https://mydomain.com/cpanel', $account->cpanel_url);
});

it('can get whm external url', function () {
    $server = Server::factory()->create(['address' => '1.1.1.1', 'port' => 2087]);
    $account = Account::factory()->make(['server_id' => $server->id, 'domain' => 'mydomain.com']);

    $this->assertEquals('https://1.1.1.1:2087', $account->server->whm_url);
});

it('can get domain external url', function () {
    $account = Account::factory()->make(['domain' => 'mydomain.com']);

    $this->assertEquals('https://mydomain.com', $account->domain_url);
});

it('can get disk usage', function () {
    $accountA = Account::factory()->make(['disk_used' => '300M', 'disk_limit' => '2000M']);
    $accountB = Account::factory()->make(['disk_used' => '350M', 'disk_limit' => '2000M']);
    $accountC = Account::factory()->make(['disk_used' => '400M', 'disk_limit' => 'unlimited']);

    $this->assertEquals('15%', $accountA->formatted_disk_usage);
    $this->assertEquals('17.5%', $accountB->formatted_disk_usage);
    $this->assertEquals('Unknown', $accountC->formatted_disk_usage);
});

it('can determine disk is at warning level', function () {
    // 85% usage: >= 80 (warning) and < 90 (critical)
    $account = Account::factory()->make(['disk_used' => '850M', 'disk_limit' => '1000M']);

    expect($account->is_disk_warning)->toBeTrue();
    expect($account->is_disk_critical)->toBeFalse();
    expect($account->is_disk_full)->toBeFalse();
});

it('can determine disk is at critical level', function () {
    // 92% usage: >= 90 (critical) and < 98 (full)
    $account = Account::factory()->make(['disk_used' => '920M', 'disk_limit' => '1000M']);

    expect($account->is_disk_warning)->toBeFalse();
    expect($account->is_disk_critical)->toBeTrue();
    expect($account->is_disk_full)->toBeFalse();
});

it('can determine disk is full', function () {
    // 99% usage: >= 98 (full)
    $account = Account::factory()->make(['disk_used' => '990M', 'disk_limit' => '1000M']);

    expect($account->is_disk_warning)->toBeFalse();
    expect($account->is_disk_critical)->toBeFalse();
    expect($account->is_disk_full)->toBeTrue();
});

it('returns null for disk thresholds when disk limit is unlimited', function () {
    $account = Account::factory()->make(['disk_used' => '500M', 'disk_limit' => 'unlimited']);

    expect($account->is_disk_warning)->toBeNull();
    expect($account->is_disk_critical)->toBeNull();
    expect($account->is_disk_full)->toBeNull();
});

it('can determine backups are enabled', function () {
    $account = Account::factory()->make(['backup' => true]);

    expect($account->backups_enabled)->toBeTrue();
});

it('can determine backups are disabled', function () {
    $account = Account::factory()->make(['backup' => false]);

    expect($account->backups_enabled)->toBeFalse();
});

it('can export account data with specific columns', function () {
    $server = Server::factory()->create(['name' => 'My Server']);
    $account = Account::factory()->create([
        'server_id' => $server->id,
        'domain' => 'testdomain.com',
        'user' => 'testuser',
        'disk_used' => '500M',
        'disk_limit' => '1000M',
    ]);

    $exported = $account->export(['domain', 'username', 'disk_used']);

    expect($exported)->toBe([
        'domain' => 'testdomain.com',
        'username' => 'testuser',
        'disk_used' => '500M',
    ]);
});

it('can export all account data columns', function () {
    $server = Server::factory()->create(['name' => 'My Server']);
    $account = Account::factory()->create([
        'server_id' => $server->id,
        'domain' => 'testdomain.com',
        'user' => 'testuser',
        'ip' => '1.2.3.4',
        'backup' => true,
        'suspended' => false,
        'suspend_reason' => 'not suspended',
        'suspend_time' => null,
        'disk_used' => '500M',
        'disk_limit' => '1000M',
        'plan' => '1 Gig',
        'wordpress_version' => null,
    ]);

    $exported = $account->export(['domain', 'server', 'username', 'ip']);

    expect($exported)->toHaveKeys(['domain', 'server', 'username', 'ip'])
        ->and($exported['domain'])->toBe('testdomain.com')
        ->and($exported['server'])->toBe('My Server')
        ->and($exported['username'])->toBe('testuser')
        ->and($exported['ip'])->toBe('1.2.3.4');
});

it('sets wordpress version to null when feed returns non-ok response', function () {
    Http::fake(['*' => Http::response('Not Found', 404)]);

    $account = Account::factory()->create(['domain' => 'testdomain.com']);

    $account->checkWordPress();

    expect($account->fresh()->wordpress_version)->toBeNull();
});

it('sets wordpress version to null on exception', function () {
    Http::fake(['*' => function () {
        throw new \Exception('Connection failed');
    }]);

    $account = Account::factory()->create(['domain' => 'testdomain.com']);

    $account->checkWordPress();

    expect($account->fresh()->wordpress_version)->toBeNull();
});
