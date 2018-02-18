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

Route::get('/user/verify/{user_id}/{token}', 'Auth\RegisterController@verify_user');

Route::get('/email/verify/{token}', 'UserController@verify_email');

Route::get('/profile', 'UserController@profile');

Route::get('/campaigns/{id}/edit/database', 'CampaignController@edit_database');
Route::get('/campaigns/{id}/edit/content', 'CampaignController@edit_content');
Route::resource('campaigns', 'CampaignController');

