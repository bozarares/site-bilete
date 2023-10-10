<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Order routes
    Route::get('/orders', [OrderController::class, 'index'])->name(
        'orders.index'
    );

    // Payment routes
    Route::post('/checkout', [PaymentController::class, 'store'])->name(
        'checkout'
    );
    Route::get('/checkout/success', [
        PaymentController::class,
        'success',
    ])->name('checkout.success');
    Route::get('/checkout/failure', [
        PaymentController::class,
        'failure',
    ])->name('checkout.failure');
});

// Public webhook route
