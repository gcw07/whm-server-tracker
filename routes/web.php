<?php

use App\Http\Controllers\AccountsController;
use App\Http\Controllers\Api\AccountsListingsController;
use App\Http\Controllers\Api\DashboardLatestAccountsController;
use App\Http\Controllers\Api\DashboardServersController;
use App\Http\Controllers\Api\DashboardStatsController;
use App\Http\Controllers\Api\ServersListingsController;
use App\Http\Controllers\Api\UsersListingsController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FetchAccountsController;
use App\Http\Controllers\FetchDetailsController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ServersController;
use App\Http\Controllers\ServersTokenController;
use App\Http\Controllers\UsersChangePasswordController;
use App\Http\Controllers\UsersController;

Route::redirect('/', '/dashboard');

// Authentication Routes...
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Password Reset Routes...
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset']);

// Dashboard Routes...
Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Account Routes...
Route::group(['middleware' => 'auth', 'prefix' => 'accounts'], function () {
    Route::get('/', [AccountsController::class, 'index'])->name('accounts.index');
    Route::get('/{server}', [AccountsController::class, 'index'])->name('accounts.server-index');
});

// Server Routes...
Route::group(['middleware' => 'auth', 'prefix' => 'servers'], function () {
    Route::post('/', [ServersController::class, 'store'])->name('servers.store');
    Route::get('/', [ServersController::class, 'index'])->name('servers.index');
    Route::delete('/{server}', [ServersController::class, 'destroy'])->name('servers.destroy');
    Route::put('/{server}', [ServersController::class, 'update'])->name('servers.update');
    Route::get('/{server}', [ServersController::class, 'show'])->name('servers.show');
    Route::get('/{server}/edit', [ServersController::class, 'edit'])->name('servers.edit');

    Route::put('/{server}/token', [ServersTokenController::class, 'update'])->name('servers.token');
    Route::delete('/{server}/token', [ServersTokenController::class, 'destroy'])->name('servers.token-destroy');

    Route::get('/{server}/fetch-details', [FetchDetailsController::class, 'update'])->name('servers.fetch-details');
    Route::get('/{server}/fetch-accounts', [FetchAccountsController::class, 'update'])->name('servers.fetch-accounts');
});

// User Routes...
Route::group(['middleware' => 'auth', 'prefix' => 'users'], function () {
    Route::post('/', [UsersController::class, 'store'])->name('users.store');
    Route::get('/', [UsersController::class, 'index'])->name('users.index');
    Route::delete('/{user}', [UsersController::class, 'destroy'])->name('users.destroy');
    Route::put('/{user}', [UsersController::class, 'update'])->name('users.update');
    Route::get('/{user}/edit', [UsersController::class, 'edit'])->name('users.edit');

    Route::put('/{user}/change-password', [UsersChangePasswordController::class, 'update'])->name('users.change-password');
});

// Search Routes...
Route::group(['middleware' => 'auth'], function () {
    Route::get('/search', [SearchController::class, 'index'])->name('search');
});

// API Routes...
Route::group(['middleware' => 'auth', 'prefix' => 'api'], function () {
    Route::get('/accounts', [AccountsListingsController::class, 'index'])->name('accounts.listing');
    Route::get('/accounts/{server}', [AccountsListingsController::class, 'index'])->name('accounts.server-listing');
    Route::get('/dashboard/stats', [DashboardStatsController::class, 'index'])->name('dashboard.stats');
    Route::get('/dashboard/servers', [DashboardServersController::class, 'index'])->name('dashboard.servers');
    Route::get('/dashboard/latest-accounts', [DashboardLatestAccountsController::class, 'index'])->name('dashboard.latest-accounts');
    Route::get('/servers', [ServersListingsController::class, 'index'])->name('servers.listing');
    Route::get('/users', [UsersListingsController::class, 'index'])->name('users.listing');
});
