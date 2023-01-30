<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Livewire\Account\Details as AccountDetails;
use App\Http\Livewire\Account\Listings as AccountListings;
use App\Http\Livewire\Dashboard;
use App\Http\Livewire\Monitor\Details as MonitorDetails;
use App\Http\Livewire\Monitor\LighthouseFrame as MonitorLighthouseFrame;
use App\Http\Livewire\Monitor\LighthouseReport as MonitorLighthouseReport;
use App\Http\Livewire\Monitor\Listings as MonitorListings;
use App\Http\Livewire\Search;
use App\Http\Livewire\Server\Create as ServerCreate;
use App\Http\Livewire\Server\Details as ServerDetails;
use App\Http\Livewire\Server\Edit as ServerEdit;
use App\Http\Livewire\Server\Listings as ServerListings;
use App\Http\Livewire\User\Create as UserCreate;
use App\Http\Livewire\User\Edit as UserEdit;
use App\Http\Livewire\User\Listings as UserListings;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

// Authentication Routes...
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Password Reset Routes...
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// Dashboard Routes...
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
});

// Account Routes...
Route::prefix('accounts')->middleware('auth')->group(function () {
    Route::get('/', AccountListings::class)->name('accounts.index');
    Route::get('/{account}', AccountDetails::class)->name('accounts.show');
});

// Server Routes...
Route::prefix('servers')->middleware('auth')->group(function () {
    Route::get('/', ServerListings::class)->name('servers.index');
    Route::get('/create', ServerCreate::class)->name('servers.create');
    Route::get('/{server}', ServerDetails::class)->name('servers.show');
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
