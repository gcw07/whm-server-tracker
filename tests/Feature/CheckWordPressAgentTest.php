<?php

use App\Enums\WordPressStatusEnum;
use App\Models\Monitor;
use App\Models\MonitorWordPressCheck;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Spatie\UptimeMonitor\Database\Factories\MonitorFactory;

uses(LazilyRefreshDatabase::class);

function agentPayload(array $overrides = []): array
{
    return array_merge([
        'agent' => ['name' => 'WP Tracker Agent', 'version' => '0.9.0'],
        'site' => [
            'url' => 'https://myserver.com',
            'name' => 'Test Site',
            'wordpress_version' => '6.4.2',
            'php_version' => '8.4.1',
            'timezone' => '+00:00',
        ],
        'theme' => ['name' => 'Avada', 'version' => '7.14'],
        'counts' => ['plugins_installed' => 2, 'themes_installed' => 1],
        'plugins' => [
            ['name' => 'Akismet', 'file' => 'akismet/akismet.php', 'version' => '5.3', 'active' => true],
            ['name' => 'Jetpack', 'file' => 'jetpack/jetpack.php', 'version' => '13.0', 'active' => false],
        ],
        'themes' => [
            ['name' => 'Avada', 'slug' => 'Avada', 'version' => '7.14', 'active' => true],
        ],
        'updates' => ['plugins' => [], 'themes' => []],
        'generated_at' => '2026-04-21 18:58:47',
    ], $overrides);
}

function makeMonitorWithToken(string $url = 'https://myserver.com'): Monitor
{
    MonitorFactory::new()->create(['url' => $url, 'wp_api_token' => 'test-token-abc123']);
    $monitor = Monitor::first();
    MonitorWordPressCheck::create(['monitor_id' => $monitor->id, 'enabled' => true]);

    return $monitor->fresh();
}

test('checkWordPress uses agent when wp_api_token is set', function () {
    Http::fake([
        'https://myserver.com/wp-json/tracker/v1/status' => Http::response(agentPayload(), 200),
    ]);

    $monitor = makeMonitorWithToken();
    $monitor->checkWordPress();

    $check = $monitor->wordpressCheck->fresh();
    expect($check->status)->toBe(WordPressStatusEnum::Valid);
    expect($check->check_source)->toBe('agent');
    expect($check->wordpress_version)->toBe('6.4.2');
    expect($check->php_version)->toBe('8.4.1');
    expect($check->site_name)->toBe('Test Site');
    expect($check->active_theme)->toBe('Avada');
    expect($check->active_theme_version)->toBe('7.14');
    expect($check->plugins_installed_count)->toBe(2);
    expect($check->agent_version)->toBe('0.9.0');
    expect($check->failure_reason)->toBeNull();
});

test('checkWordPress stores plugins via agent', function () {
    Http::fake([
        'https://myserver.com/wp-json/tracker/v1/status' => Http::response(agentPayload(), 200),
    ]);

    $monitor = makeMonitorWithToken();
    $monitor->checkWordPress();

    $monitor->load('wpPlugins');
    expect($monitor->wpPlugins)->toHaveCount(2);

    $akismet = $monitor->wpPlugins->firstWhere('file', 'akismet/akismet.php');
    expect($akismet->name)->toBe('Akismet');
    expect($akismet->version)->toBe('5.3');
    expect($akismet->active)->toBeTrue();
    expect($akismet->update_available)->toBeFalse();

    $jetpack = $monitor->wpPlugins->firstWhere('file', 'jetpack/jetpack.php');
    expect($jetpack->active)->toBeFalse();
});

test('checkWordPress stores themes via agent', function () {
    Http::fake([
        'https://myserver.com/wp-json/tracker/v1/status' => Http::response(agentPayload(), 200),
    ]);

    $monitor = makeMonitorWithToken();
    $monitor->checkWordPress();

    $monitor->load('wpThemes');
    expect($monitor->wpThemes)->toHaveCount(1);

    $theme = $monitor->wpThemes->first();
    expect($theme->name)->toBe('Avada');
    expect($theme->slug)->toBe('Avada');
    expect($theme->active)->toBeTrue();
    expect($theme->update_available)->toBeFalse();
});

test('agent marks plugins with available updates', function () {
    $payload = agentPayload(['updates' => ['plugins' => ['akismet/akismet.php'], 'themes' => []]]);

    Http::fake([
        'https://myserver.com/wp-json/tracker/v1/status' => Http::response($payload, 200),
    ]);

    $monitor = makeMonitorWithToken();
    $monitor->checkWordPress();

    $monitor->load('wpPlugins');
    $akismet = $monitor->wpPlugins->firstWhere('file', 'akismet/akismet.php');
    expect($akismet->update_available)->toBeTrue();

    $jetpack = $monitor->wpPlugins->firstWhere('file', 'jetpack/jetpack.php');
    expect($jetpack->update_available)->toBeFalse();

    $check = $monitor->wordpressCheck->fresh();
    expect($check->plugin_updates_count)->toBe(1);
});

test('agent sets invalid status on non-ok response', function () {
    Http::fake([
        'https://myserver.com/wp-json/tracker/v1/status' => Http::response('Unauthorized', 401),
    ]);

    $monitor = makeMonitorWithToken();
    $monitor->checkWordPress();

    $check = $monitor->wordpressCheck->fresh();
    expect($check->status)->toBe(WordPressStatusEnum::Invalid);
    expect($check->failure_reason)->toContain('401');
});

test('agent sets invalid status on connection exception', function () {
    Http::fake([
        'https://myserver.com/wp-json/tracker/v1/status' => fn () => throw new ConnectionException('Connection refused'),
    ]);

    $monitor = makeMonitorWithToken();
    $monitor->checkWordPress();

    $check = $monitor->wordpressCheck->fresh();
    expect($check->status)->toBe(WordPressStatusEnum::Invalid);
    expect($check->failure_reason)->not->toBeEmpty();
});

test('checkWordPress uses rss fallback when no wp_api_token', function () {
    $feedXml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
  <channel>
    <generator>https://wordpress.org/?v=6.4.2</generator>
  </channel>
</rss>
XML;

    Http::fake(['https://myserver.com/feed/' => Http::response($feedXml, 200)]);

    MonitorFactory::new()->create(['url' => 'https://myserver.com', 'wp_api_token' => null]);
    $monitor = Monitor::first();
    MonitorWordPressCheck::create(['monitor_id' => $monitor->id, 'enabled' => true]);

    $monitor->checkWordPress();

    $check = $monitor->wordpressCheck->fresh();
    expect($check->status)->toBe(WordPressStatusEnum::Valid);
    expect($check->check_source)->toBe('rss');
    expect($check->wordpress_version)->toBe('6.4.2');
});

test('agent replaces previously stored plugins on re-check', function () {
    $newPayload = agentPayload([
        'plugins' => [
            ['name' => 'Hello Dolly', 'file' => 'hello-dolly/hello.php', 'version' => '1.7.2', 'active' => true],
        ],
        'counts' => ['plugins_installed' => 1, 'themes_installed' => 1],
    ]);

    Http::fake([
        'https://myserver.com/wp-json/tracker/v1/status' => Http::sequence()
            ->push(agentPayload(), 200)
            ->push($newPayload, 200),
    ]);

    $monitor = makeMonitorWithToken();
    $monitor->checkWordPress();
    expect($monitor->wpPlugins()->count())->toBe(2);

    $monitor->checkWordPress();
    expect($monitor->wpPlugins()->count())->toBe(1);
    expect($monitor->wpPlugins()->first()->name)->toBe('Hello Dolly');
});
