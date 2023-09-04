<?php

use Illuminate\Support\Facades\Route;
use ShipSaasReducer\Tests\App\Http\Controllers\UserController;

Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{userId}', [UserController::class, 'show']);
