<?php

namespace App\Services\Google;

use App\Models\Setting;
use Carbon\Carbon;
use Google\Client;
use RuntimeException;

class GoogleApiClientService
{
    public function getClient(): Client
    {
        $client = new Client;
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));

        $oauth = Setting::getValue('google_oauth');

        throw_unless($oauth, new RuntimeException('Google OAuth is not configured. Please connect your Google account.'));

        $expiresIn = (int) $oauth['expires_in'];

        $client->setAccessToken([
            'access_token' => $oauth['access_token'],
            'refresh_token' => $oauth['refresh_token'],
            'expires_in' => $expiresIn,
            'created' => Carbon::parse($oauth['expires_at'])->timestamp - $expiresIn,
        ]);

        if ($client->isAccessTokenExpired()) {
            $newToken = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());

            throw_if(isset($newToken['error']), new RuntimeException(
                    "Google OAuth token refresh failed: {$newToken['error_description']}. Please reconnect your Google account."
                ));

            Setting::setValue('google_oauth', array_merge($oauth, [
                'access_token' => $newToken['access_token'],
                'expires_in' => $newToken['expires_in'],
                'expires_at' => Carbon::createFromTimestamp($newToken['created'])
                    ->addSeconds($newToken['expires_in'])
                    ->toIso8601String(),
            ]));
        }

        return $client;
    }
}
