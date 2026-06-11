<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Laravel\Socialite\Facades\Socialite;

class GoogleOAuthController
{
    public function redirect()
    {
        return Socialite::driver('google')
            ->scopes(['https://www.googleapis.com/auth/webmasters'])
            ->with(['access_type' => 'offline', 'prompt' => 'consent'])
            ->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->user();

        Setting::setValue('google_oauth', [
            'access_token' => $googleUser->token,
            'refresh_token' => $googleUser->refreshToken,
            'expires_in' => $googleUser->expiresIn,
            'expires_at' => now()->addSeconds($googleUser->expiresIn)->toIso8601String(),
            'email' => $googleUser->getEmail(),
        ]);

        session()->flash('settings.toast', [
            'text' => 'Google Search Console connected successfully.',
            'heading' => 'Connected!',
            'variant' => 'success',
        ]);

        return redirect()->route('settings.index');
    }

    public function disconnect()
    {
        Setting::where('key', 'google_oauth')->delete();

        session()->flash('settings.toast', [
            'text' => 'Google Search Console has been disconnected.',
            'heading' => 'Disconnected',
            'variant' => 'success',
        ]);

        return redirect()->route('settings.index');
    }
}
