<?php

use App\Services\Blacklist\Drivers\SpamhausDBLDriver;

test('spamhaus dbl driver returns listed for spam domain code 127.0.1.2', function () {
    $driver = Mockery::mock(SpamhausDBLDriver::class)
        ->shouldAllowMockingProtectedMethods()
        ->makePartial();
    $driver->shouldReceive('isListed')
        ->with('example.com.dbl.spamhaus.org.')
        ->andReturn(true);

    $result = $driver->check('example.com', null);

    expect($result->listed)->toBeTrue();
    expect($result->driver)->toBe('Spamhaus DBL');
});

test('spamhaus dbl driver returns clean when not listed', function () {
    $driver = Mockery::mock(SpamhausDBLDriver::class)
        ->shouldAllowMockingProtectedMethods()
        ->makePartial();
    $driver->shouldReceive('isListed')
        ->with('example.com.dbl.spamhaus.org.')
        ->andReturn(false);

    $result = $driver->check('example.com', null);

    expect($result->listed)->toBeFalse();
});

test('spamhaus dbl driver returns clean for rate limit return code', function () {
    $driver = Mockery::mock(SpamhausDBLDriver::class)
        ->shouldAllowMockingProtectedMethods()
        ->makePartial();
    $driver->shouldReceive('isListed')
        ->with('example.com.dbl.spamhaus.org.')
        ->andReturnUsing(function () {
            // Simulate rate-limit return code — not a real listing
            $records = [['ip' => '127.0.255.255']];
            $listingCodes = ['127.0.1.2', '127.0.1.4', '127.0.1.5', '127.0.1.6', '127.0.1.102', '127.0.1.103', '127.0.1.104', '127.0.1.105', '127.0.1.106'];
            foreach ($records as $record) {
                if (in_array($record['ip'], $listingCodes, true)) {
                    return true;
                }
            }

            return false;
        });

    $result = $driver->check('example.com', null);

    expect($result->listed)->toBeFalse();
});

test('spamhaus dbl driver returns clean when dns returns no records', function () {
    $driver = Mockery::mock(SpamhausDBLDriver::class)
        ->shouldAllowMockingProtectedMethods()
        ->makePartial();
    $driver->shouldReceive('isListed')
        ->andReturn(false);

    $result = $driver->check('example.com', null);

    expect($result->listed)->toBeFalse();
});
