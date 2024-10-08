<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionController;


Route::post('login', UserController::class . '@login');
Route::post('register', UserController::class . '@register');

Route::controller(UserController::class)->middleware('auth:sanctum')->group(function () {
    Route::get('users', 'index');
    Route::get('users/{id}', 'show');
    Route::put('users/{id}', 'update');
    Route::delete('users/{id}', 'destroy');
});

Route::controller(TransactionController::class)->middleware('auth:sanctum')->group(function () {
    Route::get('transactions', 'index');
    Route::post('transactions', 'store');
    Route::get('transactions/{id}', 'show');
    Route::put('transactions/{id}', 'update');
    Route::delete('transactions/{id}', 'destroy');
});

