<?php

namespace App\Services\WordPress;

use App\Enums\WordPressStatusEnum;
use App\Models\Monitor;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;

class WordPressChecker
{
    public function check(Monitor $monitor): void
    {
        if ($monitor->wp_api_token) {
            $this->checkViaAgent($monitor);
        } else {
            $this->checkViaRss($monitor);
        }
    }

    private function checkViaRss(Monitor $monitor): void
    {
        try {
            $response = Http::timeout(30)->get((string) $monitor->url.'/feed/');

            if (! $response->ok()) {
                $this->setWordPress($monitor, null);

                return;
            }

            $xml = simplexml_load_string($response->body());

            if ($xml === false) {
                $this->setWordPress($monitor, null);
            } elseif ($xml->channel->generator && str_contains((string) $xml->channel->generator, '?v=')) {
                [, $version] = explode('?v=', (string) $xml->channel->generator);
                $this->setWordPress($monitor, $version);
            } else {
                $this->setWordPress($monitor, null);
            }
        } catch (Exception $exception) {
            $this->setException($monitor, $exception);
        }
    }

    private function checkViaAgent(Monitor $monitor): void
    {
        try {
            $response = Http::timeout(30)
                ->withToken($monitor->wp_api_token)
                ->get((string) $monitor->url.'/wp-json/tracker/v1/status');

            if (! $response->ok()) {
                $this->setException($monitor, new Exception("Agent returned HTTP {$response->status()}"));

                return;
            }

            $this->storeAgentData($monitor, $response->json());
        } catch (Exception $exception) {
            $this->setException($monitor, $exception);
        }
    }

    private function storeAgentData(Monitor $monitor, array $data): void
    {
        $pluginUpdateFiles = collect($data['updates']['plugins'] ?? []);
        $themeUpdateSlugs = collect($data['updates']['themes'] ?? []);

        $monitor->wordpressCheck->update([
            'status' => WordPressStatusEnum::Valid->value,
            'wordpress_version' => $data['site']['wordpress_version'] ?? null,
            'php_version' => $data['site']['php_version'] ?? null,
            'site_name' => $data['site']['name'] ?? null,
            'active_theme' => $data['theme']['name'] ?? null,
            'active_theme_version' => $data['theme']['version'] ?? null,
            'plugins_installed_count' => $data['counts']['plugins_installed'] ?? null,
            'themes_installed_count' => $data['counts']['themes_installed'] ?? null,
            'plugin_updates_count' => $pluginUpdateFiles->count(),
            'theme_updates_count' => $themeUpdateSlugs->count(),
            'check_source' => 'agent',
            'agent_version' => $data['agent']['version'] ?? null,
            'last_response_at' => Carbon::parse($data['generated_at'] ?? now()),
            'failure_reason' => null,
        ]);

        $monitor->wpPlugins()->delete();
        $monitor->wpPlugins()->createMany(
            collect($data['plugins'] ?? [])->map(fn (array $plugin) => [
                'name' => $plugin['name'],
                'file' => $plugin['file'],
                'version' => $plugin['version'],
                'active' => $plugin['active'],
                'update_available' => $pluginUpdateFiles->contains($plugin['file']),
            ])->all()
        );

        $monitor->wpThemes()->delete();
        $monitor->wpThemes()->createMany(
            collect($data['themes'] ?? [])->map(fn (array $theme) => [
                'name' => $theme['name'],
                'slug' => $theme['slug'],
                'version' => $theme['version'],
                'active' => $theme['active'],
                'update_available' => $themeUpdateSlugs->contains($theme['slug']),
            ])->all()
        );
    }

    public function setWordPress(Monitor $monitor, ?string $version): void
    {
        $monitor->wordpressCheck->update([
            'status' => WordPressStatusEnum::Valid->value,
            'wordpress_version' => $version,
            'check_source' => 'rss',
            'failure_reason' => null,
        ]);
    }

    public function setException(Monitor $monitor, Exception $exception): void
    {
        $monitor->wordpressCheck->update([
            'status' => WordPressStatusEnum::Invalid->value,
            'failure_reason' => $exception->getMessage(),
        ]);
    }
}
