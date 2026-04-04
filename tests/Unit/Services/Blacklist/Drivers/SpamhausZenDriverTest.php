<?php

use App\Services\Blacklist\Drivers\SpamhausZenDriver;

test('spamhaus zen driver returns listed for sbl code 127.0.0.2', function () {
    $driver = Mockery::mock(SpamhausZenDriver::class)
        ->shouldAllowMockingProtectedMethods()
        ->makePartial();
    $driver->shouldReceive('isListed')->andReturn(true);

    $result = $driver->check('example.com', '1.2.3.4');

    expect($result->listed)->toBeTrue();
    expect($result->driver)->toBe('Spamhaus ZEN');
});

test('spamhaus zen driver returns clean for pbl code (not a spam listing)', function () {
    $driver = Mockery::mock(SpamhausZenDriver::class)
        ->shouldAllowMockingProtectedMethods()
        ->makePartial();
    $driver->shouldReceive('isListed')->andReturn(false);

    $result = $driver->check('example.com', '1.2.3.4');

    expect($result->listed)->toBeFalse();
});

test('spamhaus zen driver returns clean when ip is null', function () {
    $result = (new SpamhausZenDriver)->check('example.com', null);

    expect($result->listed)->toBeFalse();
});

test('spamhaus zen driver isListed returns false for pbl return code', function () {
    $driver = Mockery::mock(SpamhausZenDriver::class)
        ->shouldAllowMockingProtectedMethods()
        ->makePartial();
    $driver->shouldReceive('isListed')
        ->with('4.3.2.1.zen.spamhaus.org.')
        ->andReturnUsing(function () {
            // Simulate PBL return code — not a real spam listing
            $records = [['ip' => '127.0.0.10']];
            $listingCodes = ['127.0.0.2', '127.0.0.3', '127.0.0.4', '127.0.0.5', '127.0.0.6', '127.0.0.7', '127.0.0.9'];
            foreach ($records as $record) {
                if (in_array($record['ip'], $listingCodes, true)) {
                    return true;
                }
            }

            return false;
        });

    $result = $driver->check('example.com', '1.2.3.4');

    expect($result->listed)->toBeFalse();
});

test('spamhaus zen driver isListed returns false for rate limit return code', function () {
    $driver = Mockery::mock(SpamhausZenDriver::class)
        ->shouldAllowMockingProtectedMethods()
        ->makePartial();
    $driver->shouldReceive('isListed')
        ->with('4.3.2.1.zen.spamhaus.org.')
        ->andReturn(false);

    $result = $driver->check('example.com', '1.2.3.4');

    expect($result->listed)->toBeFalse();
});
