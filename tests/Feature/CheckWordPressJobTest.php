<?php

use App\Enums\WordPressStatusEnum;
use App\Jobs\CheckWordPressJob;
use App\Models\Monitor;
use App\Models\MonitorWordPressCheck;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Spatie\UptimeMonitor\Database\Factories\MonitorFactory;

uses(LazilyRefreshDatabase::class);

test('check wordpress job runs against a monitor', function () {
    MonitorFactory::new()->create(['url' => 'https://myserver.com']);
    $monitor = Monitor::first();
    MonitorWordPressCheck::create(['monitor_id' => $monitor->id, 'enabled' => true]);

    $job = new CheckWordPressJob($monitor);

    expect($job->monitor->id)->toBe($monitor->id);
});

test('check wordpress job sets valid status with version when wordpress is detected', function () {
    $feedXml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
  <channel>
    <generator>https://wordpress.org/?v=6.4.2</generator>
  </channel>
</rss>
XML;

    MonitorFactory::new()->create(['url' => 'https://myserver.com']);
    $monitor = Monitor::first();
    $check = MonitorWordPressCheck::create(['monitor_id' => $monitor->id, 'enabled' => true]);

    $tmpFile = tempnam(sys_get_temp_dir(), 'wp_test_');
    file_put_contents($tmpFile, $feedXml);

    $monitor->wordpressCheck->load('monitor');
    $monitor->setWordPress('6.4.2');

    tap($check->fresh(), function (MonitorWordPressCheck $check) {
        expect($check->status)->toBe(WordPressStatusEnum::Valid);
        expect($check->wordpress_version)->toBe('6.4.2');
        expect($check->failure_reason)->toBeNull();
    });
});

test('check wordpress job sets valid status with null version when wordpress is not detected', function () {
    MonitorFactory::new()->create(['url' => 'https://myserver.com']);
    $monitor = Monitor::first();
    $check = MonitorWordPressCheck::create(['monitor_id' => $monitor->id, 'enabled' => true]);

    $monitor->setWordPress(null);

    tap($check->fresh(), function (MonitorWordPressCheck $check) {
        expect($check->status)->toBe(WordPressStatusEnum::Valid);
        expect($check->wordpress_version)->toBeNull();
        expect($check->failure_reason)->toBeNull();
    });
});

test('check wordpress job sets invalid status with failure reason on exception', function () {
    MonitorFactory::new()->create(['url' => 'https://myserver.com']);
    $monitor = Monitor::first();
    $check = MonitorWordPressCheck::create(['monitor_id' => $monitor->id, 'enabled' => true]);

    $monitor->setWordPressException(new Exception('Connection failed'));

    tap($check->fresh(), function (MonitorWordPressCheck $check) {
        expect($check->status)->toBe(WordPressStatusEnum::Invalid);
        expect($check->failure_reason)->toBe('Connection failed');
    });
});

test('checkWordPress preserves previously detected version when request times out', function () {
    $feedXml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
  <channel>
    <generator>https://wordpress.org/?v=6.4.2</generator>
  </channel>
</rss>
XML;

    Http::fake([
        'https://myserver.com/feed/' => Http::sequence()
            ->push($feedXml, 200)
            ->whenEmpty(fn () => throw new ConnectionException('cURL error 28: Operation timed out')),
    ]);

    MonitorFactory::new()->create(['url' => 'https://myserver.com']);
    $monitor = Monitor::first();
    $check = MonitorWordPressCheck::create(['monitor_id' => $monitor->id, 'enabled' => true]);

    $monitor->checkWordPress();

    tap($check->fresh(), function (MonitorWordPressCheck $check) {
        expect($check->status)->toBe(WordPressStatusEnum::Valid);
        expect($check->wordpress_version)->toBe('6.4.2');
    });

    $monitor->checkWordPress();

    tap($check->fresh(), function (MonitorWordPressCheck $check) {
        expect($check->status)->toBe(WordPressStatusEnum::Invalid);
        expect($check->wordpress_version)->toBe('6.4.2');
        expect($check->failure_reason)->toContain('timed out');
    });
});

test('checkWordPress detects version from rss feed generator tag', function () {
    $feedXml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
  <channel>
    <generator>https://wordpress.org/?v=6.4.2</generator>
  </channel>
</rss>
XML;

    Http::fake(['https://myserver.com/feed/' => Http::response($feedXml, 200)]);

    MonitorFactory::new()->create(['url' => 'https://myserver.com']);
    $monitor = Monitor::first();
    $check = MonitorWordPressCheck::create(['monitor_id' => $monitor->id, 'enabled' => true]);

    $monitor->checkWordPress();

    tap($check->fresh(), function (MonitorWordPressCheck $check) {
        expect($check->status)->toBe(WordPressStatusEnum::Valid);
        expect($check->wordpress_version)->toBe('6.4.2');
        expect($check->failure_reason)->toBeNull();
    });
});

test('checkWordPress sets null version when feed has no wordpress generator', function () {
    $feedXml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
  <channel>
    <generator>https://example.com/some-other-cms</generator>
  </channel>
</rss>
XML;

    Http::fake(['https://myserver.com/feed/' => Http::response($feedXml, 200)]);

    MonitorFactory::new()->create(['url' => 'https://myserver.com']);
    $monitor = Monitor::first();
    $check = MonitorWordPressCheck::create(['monitor_id' => $monitor->id, 'enabled' => true]);

    $monitor->checkWordPress();

    tap($check->fresh(), function (MonitorWordPressCheck $check) {
        expect($check->status)->toBe(WordPressStatusEnum::Valid);
        expect($check->wordpress_version)->toBeNull();
        expect($check->failure_reason)->toBeNull();
    });
});

test('checkWordPress sets null version when feed returns non-ok response', function () {
    Http::fake(['https://myserver.com/feed/' => Http::response('', 404)]);

    MonitorFactory::new()->create(['url' => 'https://myserver.com']);
    $monitor = Monitor::first();
    $check = MonitorWordPressCheck::create(['monitor_id' => $monitor->id, 'enabled' => true]);

    $monitor->checkWordPress();

    tap($check->fresh(), function (MonitorWordPressCheck $check) {
        expect($check->status)->toBe(WordPressStatusEnum::Valid);
        expect($check->wordpress_version)->toBeNull();
        expect($check->failure_reason)->toBeNull();
    });
});
