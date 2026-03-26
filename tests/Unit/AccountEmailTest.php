<?php

use App\Models\AccountEmail;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('formats disk used in bytes', function () {
    $email = AccountEmail::factory()->make(['disk_used' => 500]);

    expect($email->formatted_disk_used)->toBe('500 B');
});

it('formats disk used in megabytes', function () {
    $email = AccountEmail::factory()->make(['disk_used' => 2_621_440]); // 2.5 MB

    expect($email->formatted_disk_used)->toBe('2.5 MB');
});

it('formats disk used in gigabytes', function () {
    $email = AccountEmail::factory()->make(['disk_used' => 1_610_612_736]); // 1.5 GB

    expect($email->formatted_disk_used)->toBe('1.5 GB');
});

it('formats disk quota in megabytes', function () {
    $email = AccountEmail::factory()->make(['disk_quota' => 262_144_000]); // 250 MB

    expect($email->formatted_disk_quota)->toBe('250 MB');
});

it('returns unlimited for zero disk quota', function () {
    $email = AccountEmail::factory()->make(['disk_quota' => 0]);

    expect($email->formatted_disk_quota)->toBe('Unlimited');
});
