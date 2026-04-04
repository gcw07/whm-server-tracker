<?php

use App\Services\Blacklist\Drivers\BarracudaCentralDriver;

test('barracuda driver returns listed result when ip is on blocklist', function () {
    $driver = Mockery::mock(BarracudaCentralDriver::class)
        ->shouldAllowMockingProtectedMethods()
        ->makePartial();
    $driver->shouldReceive('isListed')
        ->with('4.3.2.1.b.barracudacentral.org.')
        ->andReturn(true);

    $result = $driver->check('example.com', '1.2.3.4');

    expect($result->listed)->toBeTrue();
    expect($result->driver)->toBe('Barracuda Central');
});

test('barracuda driver returns clean result when ip is not on blocklist', function () {
    $driver = Mockery::mock(BarracudaCentralDriver::class)
        ->shouldAllowMockingProtectedMethods()
        ->makePartial();
    $driver->shouldReceive('isListed')
        ->with('4.3.2.1.b.barracudacentral.org.')
        ->andReturn(false);

    $result = $driver->check('example.com', '1.2.3.4');

    expect($result->listed)->toBeFalse();
});

test('barracuda driver returns clean result when ip is null', function () {
    $driver = new BarracudaCentralDriver;

    $result = $driver->check('example.com', null);

    expect($result->listed)->toBeFalse();
});

test('barracuda driver reverses ip octets correctly', function () {
    $driver = Mockery::mock(BarracudaCentralDriver::class)
        ->shouldAllowMockingProtectedMethods()
        ->makePartial();
    $driver->shouldReceive('isListed')
        ->with('100.200.168.192.b.barracudacentral.org.')
        ->andReturn(false);

    $result = $driver->check('example.com', '192.168.200.100');

    expect($result->listed)->toBeFalse();
});
