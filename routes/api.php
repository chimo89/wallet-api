<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController; // Panggil AuthController 
use App\Http\Controllers\WalletController; 

// Mendefinisikan URL 
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');;

Route::middleware('auth:api')->group(function () {
    Route::post('/topup', [WalletController::class, 'topUp']);
    Route::post('/pay', [WalletController::class, 'pay']);
    Route::post('/transfer', [WalletController::class, 'transfer']);
    Route::get('/transactions', [WalletController::class, 'transactionReport']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
});
