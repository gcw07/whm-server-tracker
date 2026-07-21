<?php

use App\Http\Controllers\GoogleOAuthController;
use App\Http\Controllers\PluginUpdaterController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/plugin-updates')->group(function () {
    Route::get('/{slug}', [PluginUpdaterController::class, 'info']);
    Route::get('/{slug}/download', [PluginUpdaterController::class, 'download'])
        ->middleware('signed')
        ->name('plugin-updates.download');
});

Route::redirect('/', '/dashboard');

// Dashboard Routes...
Route::middleware(['auth'])->group(function () {
    Route::livewire('dashboard', 'pages::dashboard')->name('dashboard');
});

// Account Routes...
Route::prefix('accounts')->middleware('auth')->group(function () {
    Route::livewire('/', 'pages::account.listings')->name('accounts.index');
    Route::livewire('/{account}', 'pages::account.details')->name('accounts.show');
});

// Server Routes...
Route::prefix('servers')->middleware('auth')->group(function () {
    Route::livewire('/', 'pages::server.listings')->name('servers.index');
    Route::livewire('/create', 'pages::server.create')->name('servers.create');
    Route::livewire('/{server}', 'pages::server.details')->name('servers.show');
    Route::livewire('/{server}/edit', 'pages::server.edit')->name('servers.edit');
});

// Monitor Routes...
Route::prefix('monitors')->middleware('auth')->group(function () {
    Route::livewire('/', 'pages::monitor.listings')->name('monitors.index');
    Route::livewire('/{monitor}', 'pages::monitor.details')->name('monitors.show');
    Route::livewire('/{monitor}/lighthouse', 'pages::monitor.lighthouse-report')->name('monitors.lighthouse');
});

// User Routes...
Route::prefix('users')->middleware('auth')->group(function () {
    Route::livewire('/', 'pages::user.listings')->name('users.index');
    Route::livewire('/create', 'pages::user.create')->name('users.create');
    Route::livewire('/{user}/edit', 'pages::user.edit')->name('users.edit');
    Route::livewire('/{user}/change-password', 'pages::user.change-password')->name('users.change-password');
});

// Report Routes...
Route::prefix('reports')->middleware('auth')->group(function () {
    Route::livewire('/', 'pages::report.index')->name('reports.index');
    Route::livewire('/wp-updates', 'pages::report.wp-updates')->name('reports.wp-updates');
    Route::livewire('/ssl-certificates', 'pages::report.ssl-certificates')->name('reports.ssl-certificates');
    Route::livewire('/domain-expiry', 'pages::report.domain-expiry')->name('reports.domain-expiry');
    Route::livewire('/php-versions', 'pages::report.php-versions')->name('reports.php-versions');
    Route::livewire('/uptime-summary', 'pages::report.uptime-summary')->name('reports.uptime-summary');
    Route::livewire('/disk-usage', 'pages::report.disk-usage')->name('reports.disk-usage');
    Route::livewire('/lighthouse-performance', 'pages::report.lighthouse-performance')->name('reports.lighthouse-performance');
    Route::livewire('/wp-plugins', 'pages::report.wp-plugins')->name('reports.wp-plugins');
    Route::livewire('/wp-plugin-lookup', 'pages::report.wp-plugin-lookup')->name('reports.wp-plugin-lookup');
    Route::livewire('/blacklisted-sites', 'pages::report.blacklisted-sites')->name('reports.blacklisted-sites');
    Route::livewire('/suspended-accounts', 'pages::report.suspended-accounts')->name('reports.suspended-accounts');
    Route::livewire('/cloudflare-traffic', 'pages::report.cloudflare-traffic')->name('reports.cloudflare-traffic');
});

// Search Routes...
Route::middleware('auth')->group(function () {
    Route::livewire('/search', 'pages::search')->name('search');
});

// Settings Routes...
Route::middleware('auth')->group(function () {
    Route::livewire('/settings', 'pages::settings')->name('settings.index');
    Route::get('/auth/google', [GoogleOAuthController::class, 'redirect'])->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleOAuthController::class, 'callback'])->name('google.callback');
    Route::delete('/auth/google', [GoogleOAuthController::class, 'disconnect'])->name('google.disconnect');
});
