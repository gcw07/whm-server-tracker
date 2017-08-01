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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// Server Routes...
Route::group(['middleware' => 'auth', 'prefix' => 'servers'], function () {
    Route::post('/', 'ServersController@store')->name('servers.store');
    Route::get('/', 'ServersController@index')->name('servers.index');
    Route::get('/create', 'ServersController@create')->name('servers.create');
//    Route::delete('/{server}', 'ServersController@destroy')->name('servers.destroy');
    Route::put('/{server}', 'ServersController@update')->name('servers.update');
//    Route::get('/{server}', 'ServersController@show')->name('servers.show');
    Route::get('/{server}/edit', 'ServersController@edit')->name('servers.edit');
});