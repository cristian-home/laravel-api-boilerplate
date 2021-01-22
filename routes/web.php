<?php

use App\Http\Controllers\HomeController;
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

// PWA (Web App) Routes
Route::view('/', 'home');
Route::get('/#/reset-password', [HomeController::class, 'index'])->name('pwa.password.reset');
Route::get('/#/email/verify', [HomeController::class, 'index'])->name('pwa.verification.verify');
