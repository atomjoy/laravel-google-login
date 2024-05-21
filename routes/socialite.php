<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleLoginController;

Route::get('/google/redirect', [GoogleLoginController::class, 'redirect'])->name('google.redirect');
Route::get('/google/callback', [GoogleLoginController::class, 'callback'])->name('google.callback');
Route::get('/google/oauth', [GoogleLoginController::class, 'oauth'])->name('google.oauth');
Route::get('/google/logout', [GoogleLoginController::class, 'logout'])->name('google.logout');
