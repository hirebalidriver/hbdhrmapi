<?php

use App\Http\Controllers\ADMIN\AuthController;
use App\Http\Controllers\ADMIN\InclusionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(AuthController::class)->group(function() {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('reset-password', 'resetPassword');
    Route::post('change-reset-password', 'changePass');
    Route::post('change-password', 'changeProfilePass');

});

Route::group(['middleware' => 'auth:api', 'prefix' => 'auth'], function () {
    Route::controller(AuthController::class)->group(function() {
        Route::post('change-password', 'changeProfilePass');
    });

    Route::controller(InclusionController::class)->group(function() {
        Route::post('inclusion', 'index');
    });
});
