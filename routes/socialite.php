<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OauthLoginController;

Route::get('/oauth/{driver}/redirect', [OauthLoginController::class, 'redirect']);
Route::get('/oauth/{driver}/callback', [OauthLoginController::class, 'callback']);
Route::get('/oauth/{driver}/logout', [OauthLoginController::class, 'logout']);
Route::get('/oauth/{driver}/oauth', [OauthLoginController::class, 'oauth']);
