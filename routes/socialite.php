<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleLoginController;
use App\Http\Controllers\GithubLoginController;

Route::get('/google/redirect', [GoogleLoginController::class, 'redirect'])->name('google.redirect');
Route::get('/google/callback', [GoogleLoginController::class, 'callback'])->name('google.callback');
Route::get('/google/oauth', [GoogleLoginController::class, 'oauth'])->name('google.oauth');
Route::get('/google/logout', [GoogleLoginController::class, 'logout'])->name('google.logout');

Route::get('/github/redirect', [GithubLoginController::class, 'redirect'])->name('github.redirect');
Route::get('/github/callback', [GithubLoginController::class, 'callback'])->name('github.callback');
Route::get('/github/logout', [GithubLoginController::class, 'logout'])->name('github.logout');
