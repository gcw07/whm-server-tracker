<?php

namespace App\Http\Controllers;

use App\Models\Monitor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PluginUpdaterController
{
    public function info(Request $request, string $slug): JsonResponse
    {
        if ($slug !== config('wp-plugin-updater.slug')) {
            abort(404);
        }

        $header = $request->header('Authorization', '');

        if (! preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
            return response()->json(['message' => 'Missing bearer token.'], 401);
        }

        $token = trim($matches[1]);

        if (! $token || ! Monitor::where('wp_api_token', $token)->exists()) {
            return response()->json(['message' => 'Invalid token.'], 401);
        }

        $downloadUrl = URL::temporarySignedRoute(
            'plugin-updates.download',
            now()->addMinutes(15),
            ['slug' => $slug],
        );

        return response()->json([
            'version' => config('wp-plugin-updater.version'),
            'download_url' => $downloadUrl,
            'requires' => config('wp-plugin-updater.requires'),
            'tested' => config('wp-plugin-updater.tested'),
        ]);
    }

    public function download(Request $request, string $slug): BinaryFileResponse
    {
        if ($slug !== config('wp-plugin-updater.slug')) {
            abort(404);
        }

        $version = config('wp-plugin-updater.version');
        $filename = "{$slug}-v{$version}.zip";

        abort_unless(Storage::disk('wp-plugin')->exists($filename), 404);

        return response()->download(
            Storage::disk('wp-plugin')->path($filename),
            $filename,
        );
    }
}
