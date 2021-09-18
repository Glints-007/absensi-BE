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

Route::middleware('auth:sanctum')->get('v1/user-auth', function (Request $request) {
    return $request->user();
});

Route::post('v1/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('v1/register', [App\Http\Controllers\AuthController::class, 'register']);
Route::post('v1/forgot', [App\Http\Controllers\AuthController::class, 'forgot']);
Route::post('v1/reset', [App\Http\Controllers\AuthController::class, 'reset']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('v1/logout', [App\Http\Controllers\AuthController::class, 'logout']);
    Route::get('v1/users', [App\Http\Controllers\UserController::class, 'index']);
    Route::get('v1/users/{status}', [App\Http\Controllers\UserController::class, 'indexWithStatus']);
    Route::get('v1/users/{user}', [App\Http\Controllers\UserController::class, 'show']);
    Route::put('v1/users/{user}/verify', [App\Http\Controllers\UserController::class, 'verify']);
    Route::put('v1/users/{user}/reject', [App\Http\Controllers\UserController::class, 'reject']);
});
