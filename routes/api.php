<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\EmailVerified;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');



Route::post('signup', [AuthController::class, 'signup']);
Route::post('login', [AuthController::class, 'login']);
Route::post('forgot-password',[AuthController::class, 'forgotPassword']);
Route::post('reset-password',[AuthController::class, 'resetPassword']);

Route::post('verify-email', [AuthController::class, 'verifyEmail'])->middleware('auth:api');

Route::middleware(['auth:api',EmailVerified::class])->group(function () {

    Route::get('/get-user-detail', [AuthController::class, 'getUserDetail']);

});
