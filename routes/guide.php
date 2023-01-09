<?php

use App\Http\Controllers\GUIDE\AuthController;
use App\Http\Controllers\GUIDE\AvailabilityController;
use App\Http\Controllers\GUIDE\BalanceController;
use App\Http\Controllers\GUIDE\BookingController;
use App\Http\Controllers\GUIDE\TrxController;
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
        Route::post('profile/update/fcm/token', 'updateFCMToken');
    });

    Route::controller(BalanceController::class)->group(function(){
        Route::post('balance', 'balance');
    });

    Route::controller(BookingController::class)->group(function(){
        Route::post('bookings', 'index');
        Route::post('booking/detail', 'detail');
        Route::post('booking/bill/upload', 'uploadBill');
        Route::post('booking/bill/delete', 'billDelete');
        Route::post('booking/bills', 'bills');
        Route::post('tour/completed', 'complateTour');
    });

    Route::controller(TrxController::class)->group(function(){
        Route::post('transactions', 'index');
        Route::post('transaction/detail', 'detail');
    });

    Route::controller(AvailabilityController::class)->group(function() {
        Route::post('availability', 'index');
        Route::post('availability/add', 'add');
        Route::post('availability/delete', 'delete');
    });
});
