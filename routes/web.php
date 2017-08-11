<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', 'HomeController@index')->name('home');

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
    Route::get('/create', 'ServersController@create')->name('servers.create');
//    Route::delete('/{server}', 'ServersController@destroy')->name('servers.destroy');
    Route::put('/{server}', 'ServersController@update')->name('servers.update');
//    Route::get('/{server}', 'ServersController@show')->name('servers.show');
    Route::get('/{server}/edit', 'ServersController@edit')->name('servers.edit');

    Route::get('/{server}/fetch-details', 'FetchDetailsController@update')->name('servers.fetch-details');
    Route::get('/{server}/fetch-accounts', 'FetchAccountsController@update')->name('servers.fetch-accounts');
});

Route::group(['middleware' => 'auth', 'prefix' => 'api'], function () {
    Route::get('/servers', 'Api\ServersListingsController@index')->name('servers.listing');
});
