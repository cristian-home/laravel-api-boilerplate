<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoggedUserController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;

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

// Authentication Routes
Route::post('login', [LoginController::class, 'login'])->name('auth.login');
Route::post('register', [RegisterController::class, 'register'])->name(
    'auth.register',
);
Route::post('logout', [LoggedUserController::class, 'logout'])->name(
    'auth.logout',
);
Route::get('auth-check', [LoggedUserController::class, 'checkAuth'])->name(
    'auth.check',
);
Route::get('current-user', [
    LoggedUserController::class,
    'getCurrentUser',
])->name('auth.user');
Route::post('refresh-token', [
    LoginController::class,
    'refreshAccessToken',
])->name('auth.refresh');

// Email Verification Routes
Route::get('verification/verify', [
    VerificationController::class,
    'verify',
])->name('verification.verify');
Route::post('verification/resend', [
    VerificationController::class,
    'resend',
])->name('verification.resend');

// Reset Password Routes
Route::post('password/email', [
    ForgotPasswordController::class,
    'sendResetLinkEmail',
])->name('password.email');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name(
    'password.update',
);

// Users
Route::apiResource('users', UsersController::class);
