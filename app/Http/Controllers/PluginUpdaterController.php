<?php

namespace App\Http\Controllers;

use App\Models\Monitor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PluginUpdaterController
{
    public function info(Request $request, string $slug): JsonResponse
    {
        if ($slug !== config('plugin-updater.slug')) {
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
            'version' => config('plugin-updater.version'),
            'download_url' => $downloadUrl,
            'requires' => config('plugin-updater.requires'),
            'tested' => config('plugin-updater.tested'),
        ]);
    }

    public function download(Request $request, string $slug): BinaryFileResponse
    {
        if ($slug !== config('plugin-updater.slug')) {
            abort(404);
        }

        $path = config('plugin-updater.zip_path');

        abort_unless($path && file_exists($path), 404);

        return response()->download($path, $slug.'.zip');
    }
}
