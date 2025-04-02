<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\PositionController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::get('token', [AuthController::class, 'token']);

    // Route::post('users', [UserController::class, 'store']);
    // Route::get('users', [UserController::class, 'index']);
    // Route::get('users/{id}', [UserController::class, 'show']);
    Route::resource('users', UserController::class);

    Route::get('positions', [PositionController::class, 'index']);
});