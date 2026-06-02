<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::get('/', [TransactionController::class, 'index'])->name('dashboard');
    Route::post('/transactions/voice', [TransactionController::class, 'storeVoice'])->name('transactions.voice');
    Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
    Route::get('/transactions/export', [TransactionController::class, 'exportPdf'])->name('transactions.export');
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
