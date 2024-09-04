<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('login', UserController::class . '@login');
Route::post('register', UserController::class . '@register');

Route::controller(UserController::class)->middleware('auth:sanctum')->group(function () {
    Route::get('users', 'index');
    Route::get('users/{id}', 'show');
    Route::put('users/{id}', 'update');
    Route::delete('users/{id}', 'destroy');
});

