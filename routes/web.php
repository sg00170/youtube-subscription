<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/login', 'Auth\SignInController@loginIndex')->name('signinIndex');
Route::post('/login', 'Auth\SignInController@login')->name('signin');
Route::post('/logout', 'Auth\SignInController@logout')->name('signout');

Route::get('/social/google', 'Auth\SignUpController@signup')->name('signup');

Route::get('/my', 'User\UserController')->name('my')->middleware('auth');
Route::get('/my/channel', 'Channel\ChannelController')->name('channel')->middleware('auth');
Route::get('/channel', 'Channel\ChannelController@createIndex')->name('createIndex')->middleware('auth');
Route::post('/channel', 'Channel\ChannelController@create')->name('create')->middleware('auth');

Route::get('/my/subscription', 'Subscription\SubscriptionController')->name('subscription')->middleware('auth');
Route::get('/subscription', 'Subscription\SubscriptionController@getChannelList')->name('subscription.index')->middleware('auth');
Route::post('/subscription/{id}', 'Subscription\SubscriptionController@subscribe')->name('subscription.subscribe')->middleware('auth');