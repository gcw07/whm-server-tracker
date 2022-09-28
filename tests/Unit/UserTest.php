<?php

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('email address should be lowercase', function () {
    $user = User::factory()->create([
        'email' => 'JOHN@example.COM',
    ]);

    $this->assertEquals('john@example.com', $user->email);
});

it('a user can add a notification type', function () {
    $this->user->notification_types['uptime_check_failed'] = true;
    $this->user->notification_types['certificate_expires_soon'] = false;

    $this->assertCount(2, $this->user->notification_types);
    $this->assertTrue($this->user->notification_types['uptime_check_failed']);
});

it('a user can get a notification type', function () {
    $this->user->notification_types['uptime_check_failed'] = true;

    $this->assertTrue($this->user->notification_types['uptime_check_failed']);
});

it('a user can update a notification type', function () {
    $this->user->notification_types['uptime_check_failed'] = false;
    $this->assertFalse($this->user->notification_types['uptime_check_failed']);

    $this->user->notification_types['uptime_check_failed'] = true;
    $this->assertTrue($this->user->notification_types['uptime_check_failed']);
});

it('a notification type will not overwrite all notification types when a single one is specified', function () {
    $user = User::factory()->create(['notification_types' => ['uptime_check_failed' => true]]);
    $this->assertTrue($user->notification_types['uptime_check_failed']);

    $user->notification_types['certificate_expires_soon'] = false;

    $user->save();
    $user->refresh();

    $this->assertTrue($user->notification_types['uptime_check_failed']);
    $this->assertFalse($user->notification_types['certificate_expires_soon']);
});

it('a user can update multiple notification types at once', function () {
    $this->user->notification_types->merge([
        'uptime_check_failed' => true,
        'certificate_expires_soon' => false,
    ]);

    $this->assertTrue($this->user->notification_types['uptime_check_failed']);
    $this->assertFalse($this->user->notification_types['certificate_expires_soon']);
});

it('a user can remove a notification type', function () {
    $this->user->notification_types['uptime_check_failed'] = false;
    $this->assertCount(1, $this->user->notification_types);

    $this->user->notification_types->forget('uptime_check_failed');
    $this->assertCount(0, $this->user->notification_types);
});

it('a user can remove all notification types', function () {
    $this->user->notification_types['uptime_check_failed'] = false;
    $this->user->notification_types['certificate_expires_soon'] = true;
    $this->assertCount(2, $this->user->notification_types);

    $this->user->notification_types->forgetAll();
    $this->assertCount(0, $this->user->notification_types);
});
