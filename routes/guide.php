<?php

use App\Http\Controllers\Guide\AuthController;
use App\Http\Controllers\Guide\BalanceController;
use App\Http\Controllers\GUIDE\BookingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guide Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('booking', [BookingController::class, 'refid']);

Route::controller(AuthController::class)->group(function() {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('reset', 'reset');
    Route::post('check/top', 'checkOTP');
    Route::post('change/password', 'changePassword');
});

Route::group(['middleware' => 'auth.guide', 'prefix' => 'auth'], function () {
    Route::controller(AuthController::class)->group(function() {
        Route::post('me', 'me');
        Route::post('profile/update', 'updateProfile');
    });

    Route::controller(BalanceController::class)->group(function(){
        Route::post('balance', 'balance');
    });

    Route::controller(BookingController::class)->group(function(){
        Route::post('tour/completed', 'complateTour');
    });
});
