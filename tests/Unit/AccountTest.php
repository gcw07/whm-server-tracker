<?php

use App\Models\Account;
use App\Models\Server;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

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
