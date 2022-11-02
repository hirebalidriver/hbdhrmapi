<?php

use App\Http\Controllers\ADMIN\AuthController;
use App\Http\Controllers\ADMIN\ExclusionController;
use App\Http\Controllers\ADMIN\GuidesController;
use App\Http\Controllers\ADMIN\InclusionController;
use App\Http\Controllers\ADMIN\ToursController;
use App\Http\Controllers\ToursRelationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(AuthController::class)->group(function() {
    // Route::post('login', 'login');
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
        Route::post('inclusion/add', 'add');
        Route::post('inclusion/update', 'update');
        Route::post('inclusion/delete', 'delete');
    });

    Route::controller(ExclusionController::class)->group(function() {
        Route::post('exclusion', 'index');
        Route::post('exclusion/add', 'add');
        Route::post('exclusion/update', 'update');
        Route::post('exclusion/delete', 'delete');
    });

    Route::controller(ToursController::class)->group(function() {
        Route::post('tours', 'index');
        Route::post('tour/add', 'add');
        Route::post('tour/update', 'update');
        Route::post('tour/delete', 'delete');
        Route::post('tour/find', 'find');
    });

    Route::controller(ToursRelationController::class)->group(function() {
        Route::post('tourrelation/inclusion', 'inclusions');
        Route::post('tourrelation/inclusion/add', 'addInclusion');
        Route::post('tourrelation/inclusion/del', 'delInclusion');

        // Exclusion
        Route::post('tourrelation/exclusion', 'exclusions');
        Route::post('tourrelation/exclusion/add', 'addExclusion');
        Route::post('tourrelation/exclusion/del', 'delExclusion');
    });

    Route::controller(GuidesController::class)->group(function() {
        Route::post('guides', 'index');
        Route::post('guide/add', 'add');
        Route::post('guide/update', 'update');
        Route::post('guide/delete', 'delete');
    });
});
