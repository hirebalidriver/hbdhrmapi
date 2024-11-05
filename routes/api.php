<?php

use App\Http\Controllers\ADMIN\AuthController;
use App\Http\Controllers\ADMIN\AvailabilityController;
use App\Http\Controllers\ADMIN\BillController;
use App\Http\Controllers\ADMIN\BlackPeriodController;
use App\Http\Controllers\ADMIN\BookingController;
use App\Http\Controllers\ADMIN\DestinationController;
use App\Http\Controllers\ADMIN\ExclusionController;
use App\Http\Controllers\ADMIN\GuidesController;
use App\Http\Controllers\ADMIN\UserController;
use App\Http\Controllers\ADMIN\InclusionController;
use App\Http\Controllers\ADMIN\NotificationController;
use App\Http\Controllers\ADMIN\PackageController;
use App\Http\Controllers\ADMIN\PackageRelationController;
use App\Http\Controllers\ADMIN\PricesController;
use App\Http\Controllers\ADMIN\StatistikController;
use App\Http\Controllers\ADMIN\TimeController;
use App\Http\Controllers\ADMIN\ToursController;
use App\Http\Controllers\ADMIN\TrxController;
use App\Http\Controllers\FRONT\TourController;
use App\Http\Controllers\FRONT\WishlistController;
use App\Http\Controllers\ToursRelationController;
use App\Models\Prices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        Route::post('guide/find', 'find');
        Route::post('guide/update', 'update');
        Route::post('guide/delete', 'delete');

        Route::post('guides/availability', 'guideAvailability');
    });

    Route::controller(BlackPeriodController::class)->group(function() {
        Route::post('black_periods', 'index');
        Route::post('black_period/add', 'add');
        Route::post('black_period/find', 'find');
        Route::post('black_period/update', 'update');
        Route::post('black_period/delete', 'delete');
    });

    Route::controller(UserController::class)->group(function() {
        Route::post('users', 'index');
        Route::post('user/add', 'add');
        Route::post('user/find', 'find');
        Route::post('user/update', 'update');
        Route::post('user/delete', 'delete');

        Route::post('guides/availability', 'guideAvailability');
    });

    Route::controller(StatistikController::class)->group(function() {
        Route::post('count', 'count');
    });

    Route::controller(PackageController::class)->group(function() {
        Route::post('packages', 'index');
        Route::post('package/add', 'add');
        Route::post('package/update', 'update');
        Route::post('package/find', 'find');
        Route::post('package/delete', 'delete');
    });

    Route::controller(PricesController::class)->group(function() {
        Route::post('prices', 'index');
        Route::post('price/add', 'add');
        Route::post('price/delete', 'delete');
    });

    Route::controller(PackageRelationController::class)->group(function() {
        Route::post('package/relations', 'index');
        Route::post('package/relation/add', 'add');
        Route::post('package/relation/delete', 'delete');
    });

    Route::controller(BookingController::class)->group(function() {
        Route::post('bookings', 'index');
        Route::post('booking/add', 'add');
        Route::post('booking/update', 'update');
        Route::post('booking/update/guide', 'updateGuide');
        Route::post('booking/delete', 'delete');
        Route::post('booking/find', 'find');
        Route::post('booking/find/date', 'findByDate');
        Route::post('booking/options', 'getOptions');
        Route::post('booking/find/ref', 'findByRefId');
        Route::post('booking/filter', 'filter');
        Route::post('booking/update/status', 'updateStatus');
    });

    Route::controller(TrxController::class)->group(function() {
        Route::post('trx/approve/tour', 'bookingApprove');
        Route::post('trx/notapprove/tour', 'bookingNotApprove');
    });

    Route::controller(BillController::class)->group(function() {
        Route::post('bills', 'index');
        Route::post('bill/detail', 'detail');
        Route::post('bill/filter', 'filter');
        Route::post('bill/add', 'add');
        Route::post('bill/delete', 'delete');
    });

    Route::controller(DestinationController::class)->group(function() {
        Route::post('destinations', 'index');
        Route::post('destination/find', 'find');
        Route::post('destination/add', 'add');
        Route::post('destination/update', 'update');
        Route::post('destination/delete', 'delete');
    });

    Route::controller(AvailabilityController::class)->group(function() {
        Route::post('availabilities', 'index');
        Route::post('availability/off/add', 'addDayOff');
        Route::post('availability/off/del', 'removeDayOff');
    });

    Route::controller(NotificationController::class)->group(function() {
        Route::post('send/select/guide', 'selectGuide');
    });

    Route::controller(TimeController::class)->group(function() {
        Route::post('times/by/tour', 'index');
        Route::post('time/add', 'add');
        Route::post('time/delete', 'delete');
    });

    Route::controller(WishlistController::class)->group(function() {
        Route::post('wishlists', 'index');
        Route::post('wishlist/find', 'find');
    });
});


// FRONT
Route::group(['prefix' => 'front'], function () {
    Route::controller(TourController::class)->group(function() {
        Route::get('tour/detail', 'tourByID');
        Route::get('tour/options', 'optionsByTourID');
        Route::get('tour/get_black_period', 'getBlackPeriod');
    });

    Route::controller(WishlistController::class)->group(function() {
        Route::get('wishlist/detail', 'detail');
        Route::get('wishlist/add', 'add');
        Route::get('wishlist/booking', 'booking');
        Route::get('wishlist/booking/detail', 'findBooking');
        Route::get('wishlist/booking/send/mail', 'sendEmail');
        Route::get('wishlist/update', 'update');
    });
});
