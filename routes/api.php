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

Route::prefix('v1')->group(function () {
    Route::post('login', [App\Http\Controllers\AuthController::class, 'login']);
    Route::post('register', [App\Http\Controllers\AuthController::class, 'register']);
    Route::post('forgot', [App\Http\Controllers\AuthController::class, 'forgot']);
    Route::post('reset', [App\Http\Controllers\AuthController::class, 'reset']);

    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::get('user-auth', function (Request $request) {
            return $request->user();
        });
        Route::post('logout', [App\Http\Controllers\AuthController::class, 'logout']);
        Route::get('clock-history', [App\Http\Controllers\ClockController::class, 'index']);
        Route::post('clock-in', [App\Http\Controllers\ClockController::class, 'store']);
        Route::put('clock-out', [App\Http\Controllers\ClockController::class, 'update']);
        Route::group(['middleware' => ['admin']], function () {
            Route::resource('users', App\Http\Controllers\UserController::class, [
                'only' => ['index', 'show', 'destroy']
            ]);
            Route::get('users/{status}', [App\Http\Controllers\UserController::class, 'indexWithStatus']);
            Route::put('users/{user}/verify', [App\Http\Controllers\UserController::class, 'verify']);
            Route::put('users/{user}/reject', [App\Http\Controllers\UserController::class, 'reject']);
        });
    });
});
