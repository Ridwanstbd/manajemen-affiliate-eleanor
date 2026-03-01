<?php

use App\Http\Controllers\MainController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MainController::class, 'index'])->name('home');

Route::get('transactions/transfer', [TransactionController::class, 'transfer'])->name('transactions.transfer');
Route::post('transactions/transfer', [TransactionController::class, 'storeTransfer'])->name('transactions.storeTransfer');

Route::resource('transactions', TransactionController::class);