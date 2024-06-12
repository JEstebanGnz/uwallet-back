<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

/* >>>>>>>>>>>>>>>>>>>>>>>  Auth routes >>>>>>>><<<<<< */

Route::get('auth/google/redirect', [\App\Http\Controllers\AuthController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [\App\Http\Controllers\AuthController::class, 'handleGoogleCallback']);


Route::middleware(['auth:sanctum'])->group(function (){

    Route::get('user',[\App\Http\Controllers\AuthController::class, 'userInfo']);
    Route::get('logout',[\App\Http\Controllers\AuthController::class, 'logout']);
    Route::get('check-auth',[\App\Http\Controllers\AuthController::class, function () {
        return response()->json(['authenticated' => true]);
    }]);

});



