<?php

use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Auth;
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

Auth::routes();

Route::get('/', function () {
    return view('welcome');
});

Route::get('create-transaction', [TestController::class, 'createTransaction'])->name('createTransaction');
Route::get('process-transaction', [TestController::class, 'processTransaction'])->name('processTransaction');
Route::get('success-transaction', [TestController::class, 'successTransaction'])->name('successTransaction');
Route::get('cancel-transaction', [TestController::class, 'cancelTransaction'])->name('cancelTransaction');

