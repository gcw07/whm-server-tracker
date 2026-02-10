<?php

use App\Livewire\Monitor\LighthouseFrame as MonitorLighthouseFrame;
use App\Livewire\Search;
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
