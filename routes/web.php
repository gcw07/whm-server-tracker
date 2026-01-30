<?php

use App\Livewire\Account\Details as AccountDetails;
use App\Livewire\Account\Listings as AccountListings;
use App\Livewire\Dashboard;
use App\Livewire\Monitor\Details as MonitorDetails;
use App\Livewire\Monitor\LighthouseFrame as MonitorLighthouseFrame;
use App\Livewire\Monitor\LighthouseReport as MonitorLighthouseReport;
use App\Livewire\Monitor\Listings as MonitorListings;
use App\Livewire\Search;
use App\Livewire\Server\Create as ServerCreate;
use App\Livewire\Server\Details as ServerDetails;
use App\Livewire\Server\Edit as ServerEdit;
use App\Livewire\Server\Listings as ServerListings;
use App\Livewire\User\Create as UserCreate;
use App\Livewire\User\Edit as UserEdit;
use App\Livewire\User\Listings as UserListings;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

// Dashboard Routes...
Route::middleware(['auth'])->group(function () {
    Route::livewire('dashboard', 'pages::dashboard')->name('dashboard');
});

// Account Routes...
Route::prefix('accounts')->middleware('auth')->group(function () {
    Route::get('/', AccountListings::class)->name('accounts.index');
    Route::get('/{account}', AccountDetails::class)->name('accounts.show');
});

// Server Routes...
Route::prefix('servers')->middleware('auth')->group(function () {
    Route::livewire('/', 'pages::server.listings')->name('servers.index');
    Route::livewire('/create', 'pages::server.create')->name('servers.create');
    Route::livewire('/{server}', 'pages::server.details')->name('servers.show');
    Route::get('/{server}/edit', ServerEdit::class)->name('servers.edit');
});

// Monitor Routes...
Route::prefix('monitors')->middleware('auth')->group(function () {
    Route::get('/', MonitorListings::class)->name('monitors.index');
    Route::get('/{monitor}', MonitorDetails::class)->name('monitors.show');
    Route::get('/{monitor}/lighthouse', MonitorLighthouseReport::class)->name('monitors.lighthouse');
    Route::get('/{audit}/lighthouse-iframe', MonitorLighthouseFrame::class)->name('monitors.lighthouse-iframe');
});

// User Routes...
Route::prefix('users')->middleware('auth')->group(function () {
    Route::get('/', UserListings::class)->name('users.index');
    Route::get('/create', UserCreate::class)->name('users.create');
    Route::get('/{user}/edit', UserEdit::class)->name('users.edit');
});

// Search Routes...
Route::middleware('auth')->group(function () {
    Route::get('/search', Search::class)->name('search');
});
