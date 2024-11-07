<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PersonController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// yang bisa akses routes ini hanya yang punya ability acces-api/token, bukan refresh token
Route::resource('person', PersonController::class)->middleware(['auth:sanctum', 'ability:acces-api']);

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
// yang bisa akses routes ini hanya yang punya ability issue-acces-token/ refresh token, bukan token
Route::get('refresh-token', [AuthController::class, 'refreshToken'])->middleware(['auth:sanctum', 'ability:issue-acces-token']);
Route::get('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
