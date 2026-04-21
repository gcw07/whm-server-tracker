<?php

use App\Models\Monitor;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Spatie\UptimeMonitor\Database\Factories\MonitorFactory;

uses(LazilyRefreshDatabase::class);

function makeMonitorWithToken(string $token): Monitor
{
    MonitorFactory::new()->create(['url' => 'https://example.com', 'wp_api_token' => $token]);

    return Monitor::where('wp_api_token', $token)->first();
}

describe('info endpoint', function () {
    it('returns 404 for unknown slug', function () {
        $this->getJson('/api/plugin-updates/unknown-plugin', [
            'Authorization' => 'Bearer sometoken',
        ])->assertStatus(404);
    });

    it('returns 401 when authorization header is missing', function () {
        $this->getJson('/api/plugin-updates/wp-tracker-agent')
            ->assertStatus(401);
    });

    it('returns 401 when token does not match any monitor', function () {
        $this->getJson('/api/plugin-updates/wp-tracker-agent', [
            'Authorization' => 'Bearer invalid-token',
        ])->assertStatus(401);
    });

    it('returns 200 with correct JSON shape for a valid token', function () {
        makeMonitorWithToken('valid-token-abc123');

        $response = $this->getJson('/api/plugin-updates/wp-tracker-agent', [
            'Authorization' => 'Bearer valid-token-abc123',
        ])->assertStatus(200);

        $response->assertJsonStructure(['version', 'download_url', 'requires', 'tested']);
        $response->assertJsonPath('version', config('wp-plugin-updater.version'));
        $response->assertJsonPath('requires', config('wp-plugin-updater.requires'));
        $response->assertJsonPath('tested', config('wp-plugin-updater.tested'));

        expect($response->json('download_url'))->toContain(
            route('plugin-updates.download', ['slug' => 'wp-tracker-agent'])
        );
    });
});

describe('download endpoint', function () {
    it('returns 403 when signature is invalid', function () {
        $this->get('/api/plugin-updates/wp-tracker-agent/download')
            ->assertStatus(403);
    });

    it('returns 404 for unknown slug with valid signature', function () {
        $url = URL::temporarySignedRoute(
            'plugin-updates.download',
            now()->addMinutes(15),
            ['slug' => 'unknown-plugin'],
        );

        $this->get($url)->assertStatus(404);
    });

    it('returns 404 when zip file is missing', function () {
        Storage::fake('wp-plugin');

        $url = URL::temporarySignedRoute(
            'plugin-updates.download',
            now()->addMinutes(15),
            ['slug' => 'wp-tracker-agent'],
        );

        $this->get($url)->assertStatus(404);
    });

    it('downloads the zip file with a valid signed URL', function () {
        Storage::fake('wp-plugin');

        $version = config('wp-plugin-updater.version');
        Storage::disk('wp-plugin')->put("wp-tracker-agent-v{$version}.zip", 'fake zip content');

        $url = URL::temporarySignedRoute(
            'plugin-updates.download',
            now()->addMinutes(15),
            ['slug' => 'wp-tracker-agent'],
        );

        $this->get($url)
            ->assertStatus(200)
            ->assertHeader('content-disposition');
    });

    it('returns 403 for an expired signed URL', function () {
        $url = URL::temporarySignedRoute(
            'plugin-updates.download',
            now()->subMinute(),
            ['slug' => 'wp-tracker-agent'],
        );

        $this->get($url)->assertStatus(403);
    });
});
