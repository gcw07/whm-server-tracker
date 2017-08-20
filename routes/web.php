<?php

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');

// Dashboard Routes...
Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
});

// Server Routes...
Route::group(['middleware' => 'auth', 'prefix' => 'servers'], function () {
    Route::post('/', 'ServersController@store')->name('servers.store');
    Route::get('/', 'ServersController@index')->name('servers.index');
//    Route::delete('/{server}', 'ServersController@destroy')->name('servers.destroy');
    Route::put('/{server}', 'ServersController@update')->name('servers.update');
//    Route::get('/{server}', 'ServersController@show')->name('servers.show');
    Route::get('/{server}/edit', 'ServersController@edit')->name('servers.edit');

    Route::put('/{server}/token', 'ServersTokenController@update')->name('servers.token');
    Route::delete('/{server}/token', 'ServersTokenController@destroy')->name('servers.token-destroy');

    Route::get('/{server}/fetch-details', 'FetchDetailsController@update')->name('servers.fetch-details');
    Route::get('/{server}/fetch-accounts', 'FetchAccountsController@update')->name('servers.fetch-accounts');
});

// Account Routes...
Route::group(['middleware' => 'auth', 'prefix' => 'accounts'], function () {
    Route::get('/', 'AccountsController@index')->name('accounts.index');
//    Route::get('/{account}', 'AccountsController@show')->name('accounts.show');
});

// API Routes...
Route::group(['middleware' => 'auth', 'prefix' => 'api'], function () {
    Route::get('/servers', 'Api\ServersListingsController@index')->name('servers.listing');
    Route::get('/accounts', 'Api\AccountsListingsController@index')->name('accounts.listing');
});
